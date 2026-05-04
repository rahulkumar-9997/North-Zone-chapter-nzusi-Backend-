<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use App\Models\Label;
class LabelController extends Controller
{
    public function index()
    {
        $labels = Label::orderBy('id', 'desc')->get();
        return view('backend.pages.label.index', compact('labels'));
    }

    public function create(Request $request)
    {
        $form ='
        <div class="modal-body">
            <form action="'.route('label.store').'" accept-charset="UTF-8" enctype="multipart/form-data" id="addLabelForm" method="POST">
                '.csrf_field().'
                <div class="row">  
                    <div class="col-md-12 mb-3">
                        <label class="form-label">Label Name *</label>
                        <input type="text" name="name" id="name" class="form-control">
                    </div>                   
                    
                    <div class="col-md-12 mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" checked>
                            <label class="form-check-label" for="is_active">Active</label>
                        </div>
                    </div>
                    <div class="modal-footer pb-0">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary" id="saveLabelBtn">Submit</button>
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
            'is_active' => 'nullable|boolean',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }
        DB::beginTransaction();
        try {            
            $Label = Label::create([
                'title'     => $request->name,
                'is_active' => $request->has('is_active') ? 1 : 0,
            ]);
            $html = view('backend.pages.label.partials.label-list', [
                'labels' => Label::latest()->get()
            ])->render();
            DB::commit();            
            return response()->json([
                'status' => 'success',
                'message' => 'Label created successfully',
                'labelContent' => $html,
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
        $label = Label::findOrFail($id);
        $form = '
        <div class="modal-body">
            <form action="'.route('label.update', $label->id).'" 
                enctype="multipart/form-data" 
                id="editLabelForm" 
                method="POST">
                '.csrf_field().'
                '.method_field('PUT').'
                <div class="row">  
                    <div class="col-md-12 mb-3">
                        <label class="form-label">Category Name *</label>
                        <input type="text" name="name" id="name" class="form-control" value="'.$label->title.'">
                    </div>
                    
                    <div class="col-md-12 mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" 
                                id="is_active" name="is_active" value="1"
                                '.($label->status ? 'checked' : '').'>
                            <label class="form-check-label">Active</label>
                        </div>
                    </div>
                    <div class="modal-footer pb-0">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary" id="updateLabelBtn">
                            Update
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
            'is_active' => 'nullable|boolean',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }
        DB::beginTransaction();
        try {
            $label = Label::findOrFail($id);
            $label->update([
                'title' => $request->name,
                'status'=> $request->has('is_active') ? 1 : 0,
            ]);
            $html = view('backend.pages.label.partials.label-list', [
                'labels' => Label::latest()->get()
            ])->render();
            DB::commit();
            return response()->json([
                'status' => 'success',
                'message' => 'Label updated successfully',
                'labelContent' => $html,
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
            $label = Label::findOrFail($id);
            $label->delete();
            $html = view('backend.pages.label.partials.label-list', [
                'labels' => Label::latest()->get()
            ])->render();
            DB::commit();
            return response()->json([
                'status' => 'success',
                'message' => 'Label deleted successfully',
                'labelContent' => $html,
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
