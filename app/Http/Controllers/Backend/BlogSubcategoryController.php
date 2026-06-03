<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BlogCategory;
use App\Models\BlogSubcategory;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use App\Helpers\ImageHelper;
use Illuminate\Support\Facades\Cache;

class BlogSubcategoryController extends Controller
{
    public function index()
    {
        $subcategories = BlogSubcategory::with('category')->latest()->get();
        return view('backend.pages.blog.subcategory.index', compact('subcategories'));
    }

    public function create(Request $request)
    {
        $categories = BlogCategory::where('status', 1)->get();
        $options = '<option value="">Select Category</option>';
        foreach ($categories as $category) {
            $options .= '<option value="' . $category->id . '">' . $category->title . '</option>';
        }
        $form = '
        <div class="modal-body">
            <form action="' . route('blog-subcategory.store') . '" accept-charset="UTF-8" enctype="multipart/form-data" id="addBlogSubcategoryForm" method="POST">
                ' . csrf_field() . '
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label class="form-label">Select Category *</label>
                        <select class="form-select" name="blog_category_id" id="blog_category_id">
                            ' . $options . '
                        </select>
                    </div>

                    <div class="col-md-12 mb-3">
                        <label class="form-label">Subcategory Name *</label>
                        <input type="text" name="name" id="name" class="form-control">
                    </div>

                    <div class="col-md-12 mb-3">
                        <label class="form-label">Image</label>
                        <input type="file" name="image" id="image" class="form-control">
                    </div>

                    <div class="col-md-12 mb-3">
                        <label class="form-label">Short Description</label>
                        <textarea name="short_description" id="short_description" class="form-control" rows="3"></textarea>
                    </div>

                    <div class="col-md-12 mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" checked>
                            <label class="form-check-label" for="is_active">
                                Active
                            </label>
                        </div>
                    </div>

                    <div class="modal-footer pb-0">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            Close
                        </button>

                        <button type="button" class="btn btn-primary" id="saveBlogSubcategoryBtn">
                            Save Subcategory
                        </button>
                    </div>

                </div>
            </form>
        </div>
        ';

        return response()->json([
            'status' => 'success',
            'message' => 'Form loaded successfully',
            'form' => $form,
        ]);
    }


    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'blog_category_id' => 'required|exists:blog_categories,id',
            'name' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'short_description' => 'nullable|string|max:1000',
            'is_active' => 'nullable|boolean',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
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
                    'blog-subcategory',
                    null
                );
            }

            $blogSubcategory = BlogSubcategory::create([
                'blog_category_id' => $request->blog_category_id,
                'title'            => $request->name,
                'short_content'    => $request->short_description,
                'image'            => $imageName,
                'status'           => $request->has('is_active') ? 1 : 0,
            ]);
            Cache::forget('api_blog_subcategory_list');
            DB::commit();
            $html = view('backend.pages.blog.subcategory.partials.subcategory-list', [
                'subcategories' => BlogSubcategory::latest()->get()
            ])->render();
            return response()->json([
                'status' => 'success',
                'message' => 'Blog subcategory created successfully',
                'blogSubcategoryContent' => $html,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function edit(Request $request, $id)
    {
        $blogSubcategory = BlogSubcategory::findOrFail($id);
        $categories = BlogCategory::where('status', 1)->get();
        $options = '<option value="">Select Category</option>';
        foreach ($categories as $category) {
            $selected = $blogSubcategory->blog_category_id == $category->id ? 'selected' : '';
            $options .= '<option value="' . $category->id . '" ' . $selected . '>'
                . $category->title .
                '</option>';
        }
        $form = '
        <div class="modal-body">
            <form action="' . route('blog-subcategory.update', $blogSubcategory->id) . '"
                enctype="multipart/form-data"
                id="editBlogSubcategoryForm"
                method="POST">
                ' . csrf_field() . '
                ' . method_field('PUT') . '
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label class="form-label">Select Category *</label>
                        <select class="form-select" name="blog_category_id" id="blog_category_id">
                            ' . $options . '
                        </select>
                    </div>
                    <div class="col-md-12 mb-3">
                        <label class="form-label">Subcategory Name *</label>
                        <input type="text"
                            name="name"
                            id="name"
                            class="form-control"
                            value="' . $blogSubcategory->title . '">
                    </div>
                    <div class="col-md-12 mb-3">
                        <label class="form-label">Image</label>
                        <input type="file"
                            name="image"
                            id="image"
                            class="form-control">
                    </div>';
                    if ($blogSubcategory->image) {
                        $form .= '
                        <div class="col-md-12 mb-3">
                            <img src="'.asset('storage/images/blog-subcategory/thumb/'.$blogSubcategory->image).'" 
                                width="80" height="80">
                        </div>';
                    }
                    $form .= '
                    <div class="col-md-12 mb-3">
                        <label class="form-label">Short Description</label>
                        <textarea name="short_description"
                        id="short_description"
                        class="form-control"
                        rows="3">' . $blogSubcategory->short_content . '</textarea>
                    </div>
                    <div class="col-md-12 mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input"
                            type="checkbox"
                            id="is_active"
                            name="is_active"
                            value="1"
                            ' . ($blogSubcategory->status ? 'checked' : '') . '>
                            <label class="form-check-label" for="is_active">
                                Active
                            </label>
                        </div>
                    </div>
                    <div class="modal-footer pb-0">
                        <button type="button"
                        class="btn btn-secondary"
                        data-bs-dismiss="modal">
                            Close
                        </button>
                        <button type="button"
                                class="btn btn-primary"
                                id="updateBlogSubcategoryBtn">
                            Update Subcategory
                        </button>
                    </div>
                </div>
            </form>
        </div>';

        return response()->json([
            'status' => 'success',
            'message' => 'Form loaded successfully',
            'form' => $form,
        ]);
    }


    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'blog_category_id' => 'required|exists:blog_categories,id',
            'name' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'short_description' => 'nullable|string|max:1000',
            'is_active' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }
        DB::beginTransaction();
        try {
            $blogSubcategory = BlogSubcategory::findOrFail($id);
            $imageName = $blogSubcategory->image;
            if ($request->hasFile('image')) {
                $fileName = ImageHelper::generateFileName($request->name);
                $imageName = ImageHelper::uploadImage(
                    $request->file('image'),
                    $fileName,
                    'blog-subcategory',
                    $blogSubcategory->image /*DELETE OLD IMAGE */
                );
            }
            $blogSubcategory->update([
                'blog_category_id' => $request->blog_category_id,
                'title'            => $request->name,
                'short_content'    => $request->short_description,
                'image'            => $imageName,
                'status'           => $request->has('is_active') ? 1 : 0,
            ]);
            Cache::forget('api_blog_subcategory_list');
            DB::commit();
            $html = view('backend.pages.blog.subcategory.partials.subcategory-list', [
                'subcategories' => BlogSubcategory::latest()->get()
            ])->render();
            return response()->json([
                'status' => 'success',
                'message' => 'Blog subcategory updated successfully',
                'blogSubcategoryContent' => $html,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
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
            $blogSubcategory = BlogSubcategory::findOrFail($id);
            $total = BlogSubcategory::count();
            if (!empty($blogSubcategory->image)) {
                ImageHelper::deleteImage(
                    $blogSubcategory->image,
                    'blog-subcategory'
                );
            }
            if ($total === 1) {
                $folderPath = storage_path(
                    'app/public/images/blog-subcategory'
                );
                if (File::exists($folderPath)) {
                    File::deleteDirectory($folderPath);
                }
            }
            $blogSubcategory->delete();
            Cache::forget('api_blog_subcategory_list');
            $html = view('backend.pages.blog.subcategory.partials.subcategory-list', [
                'subcategories' => BlogSubcategory::latest()->get()
            ])->render();
            DB::commit();
            return response()->json([
                'status' => 'success',
                'message' => 'Blog subcategory deleted successfully',
                'blogSubcategoryContent' => $html,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
