<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Blog extends Model
{
    protected $table = 'blogs';
    protected $fillable = [
        'category_id',
        'title',
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