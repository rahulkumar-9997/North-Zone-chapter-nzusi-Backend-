<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BlogCategory;
use App\Models\Blog;

class BlogPostController extends Controller
{
    public function index()
    {
        $blogCategories = BlogCategory::orderBy('id', 'desc')->get();
        return view('backend.pages.blog.index', compact('blogCategories'));
    }
}
