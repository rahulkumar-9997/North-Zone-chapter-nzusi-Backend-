<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use App\Models\BlogCategory;
use App\Helpers\ImageHelper;

class BlogCategoryController extends Controller
{
    public function index()
    {
        $blogCategories = BlogCategory::orderBy('id', 'desc')->get();
        return view('backend.pages.blog.category.index', compact('blogCategories'));
    }

    public function create(Request $request)
    {
        $categoryType = $request->input('category_type', 'default');
        $form ='
        <div class="modal-body">
            <form action="'.route('blog-category.store').'" accept-charset="UTF-8" enctype="multipart/form-data" id="addBlogCategoryForm" method="POST">
                '.csrf_field().'
                <input type="hidden" name="category_type" value="'.$categoryType.'">
                <div class="row">  
                    <div class="col-md-12 mb-3">
                        <label class="form-label">Category Name *</label>
                        <input type="text" name="name" id="name" class="form-control">
                    </div>
                    <div class="col-md-12 mb-3">
                        <label class="form-label">Image File</label>
                        <input type="file" name="image" id="image" class="form-control">
                    </div>
                    <div class="col-md-12 mb-3">
                        <label class="form-label">Short Description</label>
                        <textarea name="short_description" id="short_description" class="form-control" rows="2"></textarea>
                    </div>
                    
                    <div class="col-md-12 mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" checked>
                            <label class="form-check-label" for="is_active">Active</label>
                        </div>
                    </div>
                    <div class="modal-footer pb-0">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary" id="saveBlogCategoryBtn">Save Category</button>
                    </div>
                </div>
            </form>
        </div>
        ';
        
        return response()->json([
            'message' => 'Form loaded successfully',
            'form' => $form,
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'short_description' => 'nullable|string|max:500',
            'category_type' => 'nullable|string|max:50',
            'is_active' => 'nullable|boolean',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }
        DB::beginTransaction();
        try {
            $imageName = null;
            if ($request->hasFile('image')) {
                $fileName = ImageHelper::generateFileName($request->name);
                $imageName = ImageHelper::uploadImage(
                    $request->file('image'),
                    $fileName,
                    'blog-category',
                    null
                );
            }
            $blogCategory = BlogCategory::create([
                'title'              => $request->name,
                'short_description' => $request->short_description,
                'image'             => $imageName,
                'is_active'         => $request->has('is_active') ? 1 : 0,
            ]);
            $html = view('backend.pages.blog.category.partials.category-list', [
                'blogCategories' => BlogCategory::latest()->get()
            ])->render();
            DB::commit();
            return response()->json([
                'status' => 'success',
                'message' => 'Blog category created successfully',
                'category_type' => $request->category_type ?? 'default',
                'blogCategoryContent' => $html,
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function edit($id)
    {
        $blog_category = BlogCategory::findOrFail($id);
        $form = '
        <div class="modal-body">
            <form action="'.route('blog-category.update', $blog_category->id).'" 
                enctype="multipart/form-data" 
                id="editBlogCategoryForm" 
                method="POST">
                '.csrf_field().'
                '.method_field('PUT').'
                <div class="row">  
                    <div class="col-md-12 mb-3">
                        <label class="form-label">Category Name *</label>
                        <input type="text" name="name" id="name" class="form-control" value="'.$blog_category->title.'">
                    </div>
                    <div class="col-md-12 mb-3">
                        <label class="form-label">Image File</label>
                        <input type="file" name="image" id="image" class="form-control">
                    </div>';
                    if ($blog_category->image) {
                        $form .= '
                        <div class="col-md-12 mb-3">
                            <img src="'.asset('storage/images/blog-category/thumb/'.$blog_category->image).'" 
                                width="80" height="80">
                        </div>';
                    }
                    $form .= '
                    <div class="col-md-12 mb-3">
                        <label class="form-label">Short Description</label>
                        <textarea name="short_description" id="short_description" 
                        class="form-control" rows="2">'.$blog_category->short_description.'</textarea>
                    </div>
                    <div class="col-md-12 mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" 
                                id="is_active" name="is_active" value="1"
                                '.($blog_category->status ? 'checked' : '').'>
                            <label class="form-check-label">Active</label>
                        </div>
                    </div>
                    <div class="modal-footer pb-0">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary" id="updateBlogCategoryBtn">
                            Update Category
                        </button>
                    </div>
                </div>
            </form>
        </div>';
        return response()->json([
            'message' => 'Form loaded successfully',
            'form' => $form,
        ]);
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'short_description' => 'nullable|string|max:500',
            'is_active' => 'nullable|boolean',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }
        DB::beginTransaction();
        try {
            $blogCategory = BlogCategory::findOrFail($id);
            $imageName = $blogCategory->image; 
            if ($request->hasFile('image')) {
                $fileName = ImageHelper::generateFileName($request->name);
                $imageName = ImageHelper::uploadImage(
                    $request->file('image'),
                    $fileName,
                    'blog-category',
                    $blogCategory->image/*DELETE OLD IMAGE */
                );
            }
            $blogCategory->update([
                'title'             => $request->name,
                'short_description' => $request->short_description,
                'image'             => $imageName,
                'status'         => $request->has('is_active') ? 1 : 0,
            ]);
            $html = view('backend.pages.blog.category.partials.category-list', [
                'blogCategories' => BlogCategory::latest()->get()
            ])->render();
            DB::commit();
            return response()->json([
                'status' => 'success',
                'message' => 'Blog category updated successfully',
                'blogCategoryContent' => $html,
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $blogCategory = BlogCategory::findOrFail($id);
            $total = BlogCategory::count();
            if ($blogCategory->image) {
                ImageHelper::deleteImage($blogCategory->image, 'blog-category');
            }
            /* if last record → delete whole folder */
            if ($total === 1) {
                $folderPath = storage_path('app/public/images/blog-category');
                if (File::exists($folderPath)) {
                    File::deleteDirectory($folderPath);
                }
            }
            $blogCategory->delete();
            $html = view('backend.pages.blog.category.partials.category-list', [
                'blogCategories' => BlogCategory::latest()->get()
            ])->render();
            DB::commit();
            return response()->json([
                'status' => 'success',
                'message' => 'Blog category deleted successfully',
                'blogCategoryContent' => $html,
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
