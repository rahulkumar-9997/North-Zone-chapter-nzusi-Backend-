<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use App\Helpers\ImageHelper;
use App\Models\BlogCategory;
use App\Models\Blog;

class BlogPostController extends Controller
{
    public function index()
    {
        $blogCategories = BlogCategory::orderBy('id', 'desc')->get();
        return view('backend.pages.blog.index', compact('blogCategories'));
    }

    public function create()
    {
        $blogCategories = BlogCategory::orderBy('id', 'desc')->get();
        return view('backend.pages.blog.create', compact('blogCategories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id'       => 'required|exists:blog_categories,id',
            'title'             => 'required|max:255',
            'meta_title'        => 'nullable|max:255',
            'meta_description'  => 'nullable|max:500',
            'slug'              => 'nullable|max:255|unique:blogs,slug',
            'reading_title'     => 'nullable|max:255',
            'image_file'        => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'pdf_file'          => 'nullable|mimes:pdf|max:5000',
            'short_content'     => 'nullable|string',
            'long_content'      => 'nullable|string',
            'status'            => 'required|in:0,1',
        ]);
        DB::beginTransaction();
        try {
            $data = $validated;     
           

            Blog::create($data);
            DB::commit();
            return redirect()->route('blog-post.index')->with('success', 'Blog created successfully');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', $e->getMessage());
        }
    }
}
