<?php

namespace App\Http\Controllers\Backend;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Http\Request;
use App\Models\MemberType;

class MemberTypeController extends Controller
{
    public function index()
    {
        $memberTypes = MemberType::orderBy('id', 'desc')->get();
        return view('backend.pages.member.member-type.index', compact('memberTypes'));
    }

    public function create(Request $request)
    {
        $format_type = $request->input('format_type', 'default');
        $form ='
        <div class="modal-body">
            <form action="'.route('member-type.store').'" accept-charset="UTF-8" enctype="multipart/form-data" id="add-member-type-form" method="POST">
                '.csrf_field().'
                <input type="hidden" name="category_type" value="'.$format_type.'">
                <div class="row">  
                    <div class="col-md-12 mb-3">
                        <label class="form-label">Member Type Name *</label>
                        <input type="text" name="member_type_name" id="member_type_name" class="form-control">
                    </div>                                        
                    <div class="col-md-12 mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" checked>
                            <label class="form-check-label" for="is_active">Active</label>
                        </div>
                    </div>
                    <div class="modal-footer pb-0">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary" id="save-member-btn">Save Category</button>
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
            'member_type_name' => 'required|string|max:255',
            'is_active' => 'nullable|boolean',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }
        DB::beginTransaction();
        try {            
            $memberTypes = MemberType::create([
                'title'     => $request->member_type_name,
                'is_active' => $request->has('is_active') ? 1 : 0,
            ]);
            $html = view('backend.pages.member.member-type.partials.member-type-list', [
                'memberTypes' => MemberType::select('id', 'title', 'slug')->orderBy('id', 'desc')->get()
            ])->render();
            DB::commit();  
            $category_type = $request->category_type;

            if($request->category_type =='select'){
                return response()->json([
                    'status' => 'success',
                    'message' => 'Member type created successfully',
                    'category_type' => 'select',
                    'id' => $memberTypes->id,
                    'title' => $memberTypes->title
                ]);
            }else{
                return response()->json([
                    'status' => 'success',
                    'message' => 'Member type elselel created successfully',
                    'category_type' => $category_type ?? 'default',
                    'memberTypeContent' => $html,
                ]);
            }

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
        $MemberType = MemberType::findOrFail($id);
        $form ='
        <div class="modal-body">
            <form action="'.route('member-type.update', $MemberType->id).'" accept-charset="UTF-8" enctype="multipart/form-data" id="edit-member-type-form" method="POST">
                '.csrf_field().'
                '.method_field('PUT').'
                <div class="row">  
                    <div class="col-md-12 mb-3">
                        <label class="form-label">Member Type Name *</label>
                        <input type="text" name="member_type_name" id="member_type_name" class="form-control" value="'.$MemberType->title.'">
                    </div>                                        
                    <div class="col-md-12 mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" 
                                id="is_active" name="is_active" value="1"
                                '.($MemberType->status ? 'checked' : '').'>
                            <label class="form-check-label">Active</label>
                        </div>
                    </div>
                    <div class="modal-footer pb-0">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary" id="update-member-btn">Update</button>
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

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'member_type_name' => 'required|string|max:255',            
            'is_active' => 'nullable|boolean',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }
        DB::beginTransaction();
        try {
            $MemberType = MemberType::findOrFail($id);
            $MemberType->update([
                'title'   => $request->member_type_name,
                'status'  => $request->has('is_active') ? 1 : 0,
            ]);
            $html = view('backend.pages.member.member-type.partials.member-type-list', [
                'memberTypes' => MemberType::select('id', 'title', 'slug')->orderBy('id', 'desc')->get()
            ])->render();
            DB::commit();
            return response()->json([
                'status' => 'success',
                'message' => 'Member type updated successfully',
                'memberTypeContent' => $html,
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
            $MemberType = MemberType::findOrFail($id);           
            $MemberType->delete();
            $html = view('backend.pages.member.member-type.partials.member-type-list', [
                'memberTypes' => MemberType::latest()->get()
            ])->render();
            DB::commit();
            return response()->json([
                'status' => 'success',
                'message' => 'Member type deleted successfully',
                'memberTypeContent' => $html,
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
