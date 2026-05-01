<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
class Blog extends Model
{
    protected $table = 'blogs';
    protected $fillable = [
        'category_id',
        'title',
        'meta_title',
        'meta_description',
        'slug',
        'reading_title',
        'image_file',
        'pdf_file_title',
        'pdf_file',
        'short_content',
        'long_content',
        'status',
        'post_user',
        'view_count',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->slug = self::generateUniqueSlug($model->title);
        });
        static::updating(function ($model) {
            if ($model->isDirty('title')) {
                $model->slug = self::generateUniqueSlug($model->title, $model->id);
            }
        });
    }

    public static function generateUniqueSlug($title, $ignoreId = null)
    {
        $slug = Str::slug($title);
        $originalSlug = $slug;
        $count = 1;
        while (
            self::where('slug', $slug)
                ->when($ignoreId, fn($q) => $q->where('id', '!=', $ignoreId))
                ->exists()
        ) {
            $slug = $originalSlug . '-' . $count;
            $count++;
        }

        return $slug;
    }

    public function category()
    {
        return $this->belongsTo(BlogCategory::class, 'category_id');
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'post_user');
    }
    public function images()
    {
        return $this->hasMany(BlogMoreImage::class, 'blog_id');
    }
}