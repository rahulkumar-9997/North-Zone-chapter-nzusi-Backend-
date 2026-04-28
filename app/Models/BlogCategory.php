<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
class BlogCategory extends Model
{
    protected $table = 'blog_categories';
    protected $fillable = [
        'title',
        'slug',
        'short_content',
        'status',
        'image',
    ];
    public function blogs()
    {
        return $this->hasMany(Blog::class, 'category_id');
    }

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
}