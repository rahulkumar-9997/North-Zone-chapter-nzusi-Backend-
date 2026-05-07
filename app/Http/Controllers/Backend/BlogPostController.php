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
use App\Models\BlogMoreImage;
use App\Models\Label;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class BlogPostController extends Controller
{
    public function index()
    {
        $blogCategories = BlogCategory::orderBy('id', 'desc')->get();
        $blogs = Blog::with(['category', 'user', 'images', 'label'])
        ->latest()
        ->paginate(10);
        return view('backend.pages.blog.index', compact('blogCategories', 'blogs'));
    }

    public function create()
    {
        $blogCategories = BlogCategory::orderBy('id', 'desc')->get();
        $labels = Label::where('status', 1)->orderBy('id', 'desc')->get();
        return view('backend.pages.blog.create', compact('blogCategories', 'labels'));
    }

    public function store(Request $request)
    {
        //Log::info('blog', ['all' => $request->all()]); 
        $validated = $request->validate([
            'blog_category'      => 'required|exists:blog_categories,id',
            'label'      => 'required|exists:labels,id',
            'title'              => 'required|max:255',
            'meta_title'         => 'nullable|max:255',
            'meta_description'   => 'nullable|max:500',
            'reading_title'      => 'nullable|max:255',
            'main_image_file' => 'nullable|file|mimetypes:image/jpeg,image/png,image/webp,image/avif,image/gif|max:5000',
            'more_image_file'    => 'nullable|array|max:10',
            'more_image_file.*'  => 'file|mimes:jpg,jpeg,png,webp|max:2048',
            'pdf_file'           => 'nullable|mimes:pdf|max:5000',
            'pdf_file_title'     => 'nullable|max:255',
            'short_content'      => 'nullable|string',
            'long_content'       => 'nullable|string',
            'status'             => 'required|in:0,1',
        ]);
       
        DB::beginTransaction();
        try {
            $mainImage = null;
            if ($request->hasFile('main_image_file')) {
                $fileName = ImageHelper::generateFileName($request->title);
                $mainImage = ImageHelper::uploadSingleImageWebpOnly(
                    $request->file('main_image_file'),
                    $fileName,
                    'blog'
                );
            }
            $pdfFile = null;
            if ($request->hasFile('pdf_file')) {
                $pdfName = ImageHelper::generateFileName($request->title, 'pdf');
                $pdfFile = ImageHelper::uploadPdf(
                    $request->file('pdf_file'),
                    $pdfName,
                    'blog'
                );
            }
            $blog = Blog::create([
                'category_id'       => $request->blog_category,
                'label_id'       => $request->label,
                'title'             => $request->title,
                'meta_title'        => $request->meta_title,
                'meta_description'  => $request->meta_description,
                'reading_title'     => $request->reading_title,
                'image_file'        => $mainImage,
                'pdf_file'          => $pdfFile,
                'pdf_file_title'    => $request->pdf_file_title,
                'short_content'     => $request->short_content,
                'long_content'      => $request->long_content,
                'status'            => $request->status,
                'post_user' => Auth::id(),
            ]);
            if ($request->hasFile('more_image_file')) {
                foreach ($request->file('more_image_file') as $image) {
                    $fileName = ImageHelper::generateFileName($request->title, 'more');
                    $img = ImageHelper::uploadSingleImageWebpOnly(
                        $image,
                        $fileName,
                        'blog/more-images'
                    );
                    BlogMoreImage::create([
                        'blog_id'    => $blog->id,
                        'image_file' => $img,
                    ]);
                }
            }
            DB::commit();
            Cache::tags(['blogs'])->flush();
            return redirect()->route('blog-post.index')->with('success', 'Blog created successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function edit($id)
    {
        $blog = Blog::with('images')->findOrFail($id);
        $blogCategories = BlogCategory::orderBy('id', 'desc')->get();
        $labels = Label::where('status', 1)->orderBy('id', 'desc')->get();
        return view('backend.pages.blog.create', compact('blog', 'blogCategories', 'labels'));
    }

    public function update(Request $request, $id)
    {
        $blog = Blog::findOrFail($id);
        $validated = $request->validate([
            'blog_category'      => 'required|exists:blog_categories,id',
            'label'      => 'required|exists:labels,id',
            'title'              => 'required|max:255',
            'meta_title'         => 'nullable|max:255',
            'meta_description'   => 'nullable|max:500',
            'reading_title'      => 'nullable|max:255',
            'main_image_file'    => 'nullable|file|mimetypes:image/jpeg,image/png,image/webp,image/avif,image/gif|max:5000',
            'more_image_file'    => 'nullable|array|max:10',
            'more_image_file.*'  => 'file|mimes:jpg,jpeg,png,webp|max:2048',
            'pdf_file'           => 'nullable|mimes:pdf|max:5000',
            'pdf_file_title'     => 'nullable|max:255',
            'short_content'      => 'nullable|string',
            'long_content'       => 'nullable|string',
            'status'             => 'required|in:0,1',
        ]);
        DB::beginTransaction();
        try {
            $mainImage = $blog->image_file;
            if ($request->hasFile('main_image_file')) {                
                $fileName = ImageHelper::generateFileName($request->title);
                $mainImage = ImageHelper::uploadSingleImageWebpOnly(
                    $request->file('main_image_file'),
                    $fileName,
                    'blog',
                    $mainImage
                );
            }
            $pdfFile = $blog->pdf_file;
            if ($request->hasFile('pdf_file')) {
                $pdfName = ImageHelper::generateFileName($request->title, 'pdf');
                $pdfFile = ImageHelper::uploadPdf(
                    $request->file('pdf_file'),
                    $pdfName,
                    'blog',
                    $pdfFile
                );
            }
            $blog->update([
                'category_id'       => $request->blog_category,
                'label_id'       => $request->label,
                'title'             => $request->title,
                'meta_title'        => $request->meta_title,
                'meta_description'  => $request->meta_description,
                'reading_title'     => $request->reading_title,
                'image_file'        => $mainImage,
                'pdf_file'          => $pdfFile,
                'pdf_file_title'    => $request->pdf_file_title,
                'short_content'     => $request->short_content,
                'long_content'      => $request->long_content,
                'status'            => $request->status,
            ]);
            if ($request->hasFile('more_image_file')) {
                foreach ($request->file('more_image_file') as $image) {
                    $fileName = ImageHelper::generateFileName($request->title, 'more');
                    $img = ImageHelper::uploadSingleImageWebpOnly(
                        $image,
                        $fileName,
                        'blog/more-images'                       
                    );
                    BlogMoreImage::create([
                        'blog_id'    => $blog->id,
                        'image_file' => $img,
                    ]);
                }
            }
            DB::commit();
            Cache::tags(['blogs'])->flush();
            return redirect()->route('blog-post.index')->with('success', 'Blog updated successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function deleteImage($id)
    {
        $img = BlogMoreImage::findOrFail($id);
        if (File::exists(public_path($img->image_file))) {
            $folderPath = storage_path('storage/images/blog/more-images/'.$img->image_file);
            if (File::exists($folderPath)) {
                File::deleteDirectory($folderPath);
            }
        }
        $img->delete();
        return response()->json(['success' => true]);
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        Log::info('blog', ['all' => $id]); 
        try {
            $blog = Blog::with('images')->findOrFail($id);
            $totalBlogs = Blog::count();
            if (!empty($blog->image_file)) {
                ImageHelper::deleteSingleImage($blog->image_file, 'blog');
            }
            if (!empty($blog->pdf_file)) {
                ImageHelper::pdfFileDelete($blog->pdf_file, 'blog');
            }
            if ($blog->images->count()) {
                foreach ($blog->images as $img) {
                    if (!empty($img->image_file)) {
                        ImageHelper::deleteSingleImage($blog->image_file, 'blog/more-images');
                    }
                    $img->delete();
                }
            }
            if ($totalBlogs === 1) {
                $imageFolder = storage_path('app/public/images/blog');
                if (File::exists($imageFolder)) {
                    File::deleteDirectory($imageFolder);
                }
                $pdfFolder = storage_path('app/public/pdf/blog');
                if (File::exists($pdfFolder)) {
                    File::deleteDirectory($pdfFolder);
                }
            }
            $blog->delete();
            DB::commit();
            return redirect()->route('blog-post.index')->with('success', 'Blog deleted successfully.');

        } catch (\Exception $e) {
            DB::rollback();           
            return back()->with('error', $e->getMessage());
        }
    }
}
