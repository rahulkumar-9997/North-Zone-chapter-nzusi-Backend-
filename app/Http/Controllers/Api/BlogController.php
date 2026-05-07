<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use App\Models\BlogCategory;
use Illuminate\Support\Facades\Cache;

class BlogController extends Controller
{
    public function blogCategory()
    {
        $blogCategories = Cache::remember('api_blog_category_list', now()->addHours(24), function () {
        return BlogCategory::select(
                'id',
                'title',
                'slug'
            )
            ->where('status', 1)
            ->orderBy('id', 'desc')
            ->get()
            ->map(function ($category) {
                return [
                    'id' => $category->id,
                    'title' => $category->title,
                    'slug' => $category->slug,
                ];
            });
        });
        return response()->json([
            'status' => true,
            'message' => 'Blog category list',
            'data' => $blogCategories
        ]);
    }

    public function blogList()
    {
        $page = request()->input('page', 1);
        $blogs = Blog::with([
            'category:id,title,slug',
            'user:id,name',
            'images:id,blog_id,title,image_file',
            'label:id,title,slug'
            ])
            ->select(
                'id',
                'category_id',
                'post_user',
                'label_id',
                'title',
                'slug',
                'image_file',
                'pdf_file_title',
                'pdf_file',
                'short_content',
                'long_content',
                'status',
                'view_count'                
            )
        ->latest()
        ->paginate(20);
        $blogData = $blogs->getCollection()->map(function ($blog) {
            return [
                'id' => $blog->id,
                'title' => $blog->title,
                'slug' => $blog->slug,
                'image' => $blog->image_file
                    ? asset('storage/images/blog/' . $blog->image_file)
                    : null,
                'blog_category' => $blog->category ? [
                    'id' => $blog->category->id,
                    'title' => $blog->category->title,
                    'slug' => $blog->category->slug,
                ] : null,
                'user' => $blog->user ? [
                    'id' => $blog->user->id,
                    'name' => $blog->user->name,
                ] : null,
                'label' => $blog->label ? [
                    'id' => $blog->label->id,
                    'title' => $blog->label->title,
                ] : null                
            ];
        });
        $pagination = [
            'current_page' => $blogs->currentPage(),
            'total_pages' => $blogs->lastPage(),
            'per_page' => $blogs->perPage(),
            'total_products' => $blogs->total(),
            'next_page_url' => $blogs->nextPageUrl(),
            'previous_page_url' => $blogs->previousPageUrl(),
            'has_next_page' => $blogs->hasMorePages(),
            'has_previous_page' => $blogs->currentPage() > 1
        ];

        return response()->json([
            'status' => true,
            'message' => 'Blog list',
            'data' => $blogData,
            'pagination' => $pagination,
        ]);
    }

    public function categoryWiseBlogList($slug)
    {
        $blog_category = BlogCategory::select('id', 'title', 'slug')
            ->where('slug', $slug)
            ->first();
        if (!$blog_category) {
            return response()->json([
                'status' => false,
                'message' => 'Blog category not found'
            ], 404);
        }
        $blogs = Blog::with([
                'category:id,title,slug',
                'user:id,name',
                'images:id,blog_id,title,image_file',
                'label:id,title,slug'
            ])
            ->where('category_id', $blog_category->id)
            ->select(
                'id',
                'category_id',
                'post_user',
                'label_id',
                'title',
                'slug',
                'image_file',
                'pdf_file_title',
                'pdf_file',
                'short_content',
                'long_content',
                'status',
                'view_count'
            )
            ->latest()
            ->paginate(20);

        $blogData = $blogs->getCollection()->map(function ($blog) {

            return [
                'id' => $blog->id,
                'title' => $blog->title,
                'slug' => $blog->slug,
                'image' => $blog->image_file
                    ? asset('storage/images/blog/' . $blog->image_file)
                    : null,

                'blog_category' => $blog->category ? [
                    'id' => $blog->category->id,
                    'title' => $blog->category->title,
                    'slug' => $blog->category->slug,
                ] : null,

                'user' => $blog->user ? [
                    'id' => $blog->user->id,
                    'name' => $blog->user->name,
                ] : null,

                'label' => $blog->label ? [
                    'id' => $blog->label->id,
                    'title' => $blog->label->title,
                    'slug' => $blog->label->slug,
                ] : null,

                'images' => $blog->images->map(function ($image) {

                    return [
                        'id' => $image->id,
                        'title' => $image->title,

                        'image' => $image->image_file
                            ? asset('storage/images/blog/more-images/' . $image->image_file)
                            : null,
                    ];
                }),
            ];
        });

        $pagination = [
            'current_page' => $blogs->currentPage(),
            'total_pages' => $blogs->lastPage(),
            'per_page' => $blogs->perPage(),
            'total_products' => $blogs->total(),
            'next_page_url' => $blogs->nextPageUrl(),
            'previous_page_url' => $blogs->previousPageUrl(),
            'has_next_page' => $blogs->hasMorePages(),
            'has_previous_page' => $blogs->currentPage() > 1
        ];

        return response()->json([
            'status' => true,
            'message' => 'Category wise blog list',
            'category' => [
                'id' => $blog_category->id,
                'title' => $blog_category->title,
                'slug' => $blog_category->slug,
            ],
            'pagination' => $pagination,
            'data' => $blogData
        ]);
    }
}