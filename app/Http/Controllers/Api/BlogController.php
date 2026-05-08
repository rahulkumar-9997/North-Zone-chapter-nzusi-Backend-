<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use App\Models\BlogCategory;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;
use Illuminate\Support\Str;

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
                'reading_title',
                'image_file',
                'short_content',
                'long_content',
                'view_count',
                'meta_title',
                'meta_description',
                'created_at'
            )
            ->latest()
            ->paginate(20);

        $blogData = $blogs->getCollection()->map(function ($blog) {
            return [
                'id' => $blog->id,
                'title' => $blog->title,
                'reading_title' =>$blog->reading_title,
                'view_count' => $blog->view_count,
                'short_content'=> $blog->short_content,
                'long_content' => $blog->long_content,
                'slug' => $blog->slug,
                'published_at' => $blog->created_at
                    ? Carbon::parse($blog->created_at)->format('d M Y')
                    : null,
                'meta_title' => ($blog->meta_title ?? $blog->title).' | NZUSI',
                'meta_description' => $blog->meta_description 
                    ?? $blog->short_content 
                    ?? Str::limit(strip_tags($blog->long_content),160),  
                'image' => $blog->image_file
                    ? asset('storage/images/blog/' . $blog->image_file)
                    : null,
               
                'user' => $blog->user ? [
                    'id' => $blog->user->id,
                    'name' => $blog->user->name,
                ] : null,

                'label' => $blog->label ? [
                    'id' => $blog->label->id,
                    'title' => $blog->label->title,
                    'slug' => $blog->label->slug,
                ] : null,
                // 'images' => $blog->images->map(function ($image) {

                //     return [
                //         'id' => $image->id,
                //         'title' => $image->title,

                //         'image' => $image->image_file
                //             ? asset('storage/images/blog/more-images/' . $image->image_file)
                //             : null,
                //     ];
                // }),
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
            'data' => $blogData,
            'pagination' => $pagination,
            
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
                'reading_title',
                'image_file',
                'short_content',
                'long_content',
                'view_count',
                'meta_title',
                'meta_description',
                'created_at'               
            )
        ->latest()
        ->paginate(20);
        $blogData = $blogs->getCollection()->map(function ($blog) {
            return [
                'id' => $blog->id,
                'title' => $blog->title,
                'reading_title' =>$blog->reading_title,
                'view_count' => $blog->view_count,
                'short_content'=> $blog->short_content,
                'long_content' => $blog->long_content,
                'slug' => $blog->slug,
                'published_at' => $blog->created_at
                    ? Carbon::parse($blog->created_at)->format('d M Y')
                    : null,
                'meta_title' => ($blog->meta_title ?? $blog->title).' | NZUSI',
                'meta_description' => $blog->meta_description 
                    ?? $blog->short_content 
                    ?? Str::limit(strip_tags($blog->long_content),160),  
                'image' => $blog->image_file
                    ? asset('storage/images/blog/' . $blog->image_file)
                    : null,
                
                'category' => $blog->category ? [
                    'id' => $blog->category->id,
                    'name' => $blog->category->title,
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
                // 'images' => $blog->images->map(function ($image) {

                //     return [
                //         'id' => $image->id,
                //         'title' => $image->title,

                //         'image' => $image->image_file
                //             ? asset('storage/images/blog/more-images/' . $image->image_file)
                //             : null,
                //     ];
                // }),
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

    public function blogDetails($slug)
    {
        $blog = Blog::with([
                'category:id,title,slug',
                'user:id,name',
                'images:id,blog_id,title,image_file',
                'label:id,title,slug'
            ])
            ->where('slug', $slug)
            ->select(
                'id',
                'category_id',
                'post_user',
                'label_id',
                'title',
                'slug',
                'reading_title',
                'image_file',
                'short_content',
                'long_content',
                'view_count',
                'meta_title',
                'meta_description',
                'created_at'
            )
            ->first();
        if (!$blog) {
            return response()->json([
                'status' => false,
                'message' => 'Blog not found'
            ], 404);
        }

        /* Increment view count */
        $blog->increment('view_count');

        $blogData = [
            'id' => $blog->id,
            'title' => $blog->title,
            'reading_title' => $blog->reading_title,
            'view_count' => $blog->view_count + 1,
            'short_content' => $blog->short_content,
            'long_content' => $blog->long_content,
            'slug' => $blog->slug,
            'published_at' => $blog->created_at
                ? Carbon::parse($blog->created_at)->format('d M Y')
                : null,
            'meta_title' => ($blog->meta_title ?? $blog->title) . ' | NZUSI',
            'meta_description' => $blog->meta_description
                ?? $blog->short_content
                ?? Str::limit(strip_tags($blog->long_content), 160),
            'main_image' => $blog->image_file
                ? asset('storage/images/blog/' . $blog->image_file)
                : null,
            'category' => $blog->category ? [
                'id' => $blog->category->id,
                'name' => $blog->category->title,
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

        /* Recent 10 Posts */
        $recent_post = Blog::select(
                'id',
                'title',
                'slug',
                'view_count',
                'short_content',
                'long_content',
                'image_file',
                'created_at'
            )
            ->where('id', '!=', $blog->id)
            ->latest()
            ->take(10)
            ->get()
            ->map(function ($post) {
                return [
                    'id' => $post->id,
                    'title' => $post->title,
                    'slug' => $post->slug,
                    'image' => $post->image_file
                        ? asset('storage/images/blog/' . $post->image_file)
                        : null,
                    'view_count' => $post->view_count,
                    'short_content' => $post->short_content,
                    'long_content' => $post->long_content,
                    'published_at' => Carbon::parse($post->created_at)
                        ->format('d M Y'),
                ];
            });
        return response()->json([
            'status' => true,
            'message' => 'Blog Details',
            'data' => $blogData,
            'recent_post' => $recent_post,
        ]);
    }
    
}