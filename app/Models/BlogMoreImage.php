<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BlogMoreImage extends Model
{
    protected $table = 'blog_more_images';
    protected $fillable = [
        'blog_id',
        'title',
        'image_file',
    ];
    public function blog()
    {
        return $this->belongsTo(Blog::class, 'blog_id');
    }
}