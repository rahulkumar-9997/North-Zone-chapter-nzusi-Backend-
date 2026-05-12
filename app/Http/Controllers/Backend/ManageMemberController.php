<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Log;
use App\Imports\MembersImport;
use App\Models\Member;
use App\Models\MemberType;
use App\Models\MemberOfficeAddress;
use App\Models\MemberResidenceAddress;
use App\Models\MemberPresentDesignation;
use App\Models\MemberAcademicQualification;
use App\Models\MemberUrologyTraining;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;

class ManageMemberController extends Controller
{
    public function index(Request $request)
    {
        $query = Member::with([
            'type',
            'officeAddress',
            'residenceAddress',
            'presentDesignations',
            'academicQualifications',
            'trainings'
        ]);
        if ($request->member_type) {
            $query->where('membership_type_id', $request->member_type);
        }
        if ($request->status) {
            $query->where('status', $request->status);
        }
        if ($request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('membership_no', 'like', "%$search%")
                    ->orWhere('name', 'like', "%$search%")
                    ->orWhere('email', 'like', "%$search%")
                    ->orWhere('mobile_no', 'like', "%$search%");
            });
        }
        
        $sortBy = $request->input('sort_by', 'id');
        $sortOrder = $request->input('sort_order', 'desc');
        if (!in_array($sortOrder, ['asc', 'desc'])) {
            $sortOrder = 'desc';
        }
        $query->getQuery()->orders = null;
        switch ($sortBy) {
            case 'name':
                $query->orderByRaw("LOWER(name) $sortOrder");
                break;                
            case 'membership_no':
                $query->orderByRaw("LENGTH(membership_no), membership_no $sortOrder");
                break;                
            case 'id':
            default:
                $query->orderBy('id', $sortOrder);
                break;
        }
        if ($sortBy !== 'id') {
            $query->orderBy('id', 'desc');
        }
        
        $member_lists = $query->paginate(30);
        if ($request->ajax()) {
            return view('backend.pages.member.members.partials.members-list', compact('member_lists'))->render();
        }
        
        $members_type = MemberType::select('id', 'title')->get();
        return view('backend.pages.member.members.index', compact('member_lists', 'members_type'));
    }

    public function create(){

        $memberTypes = MemberType::select('id', 'title')
            ->where('status', 1)
            ->orderBy('id', 'desc')
            ->get();
        return view('backend.pages.member.members.member-registration-form.personal', compact('memberTypes'));
    }

    /* EDIT - Show edit form */
    public function edit($id)
    {
        $member = Member::with([
            'officeAddress',
            'residenceAddress',
            'presentDesignations',
            'academicQualifications',
            'trainings'
        ])->findOrFail($id);
        //return response($member);
        $memberTypes = MemberType::where('status', 1)->get();
        return view('backend.pages.member.members.member-registration-form.personal', compact('member', 'memberTypes'));
    }

    /* STORE STEP 1 (CREATE) */
    public function storeStep1(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'member_type' => 'required|exists:member_types,id',
            'membership_no' => 'required|unique:members,membership_no',
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:members,email',
            'mobile_no' => 'nullable|string|max:20',
            'gender' => 'nullable|in:male,female,other',
            'city_name' => 'nullable|string|max:255',
            'dob' => 'nullable|date',
            'preferred_address' => 'required|in:office,residence',
            'status' => 'required|in:pending,approved,rejected',
            'office_state' => 'required_if:preferred_address,office|nullable|string|max:255',
            'office_city' => 'required_if:preferred_address,office|nullable|string|max:255',
            'office_pin' => 'nullable|string|max:6',
            'office_address' => 'nullable|string|max:255',
            'office_phone' => 'nullable|string|max:20',
            'office_email' => 'nullable|email',
            'office_website' => 'nullable|url',
            'residence_state' => 'required_if:preferred_address,residence|nullable|string|max:255',
            'residence_city' => 'required_if:preferred_address,residence|nullable|string|max:255',
            'residence_pin' => 'nullable|string|max:6',
            'residence_address' => 'nullable|string|max:255',
            'residence_phone' => 'nullable|string|max:20',
            'residence_email' => 'nullable|email',
            'residence_website' => 'nullable|url',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();
        try {
            $member = Member::create([
                'membership_type_id' => $request->member_type,
                'membership_no' => $request->membership_no,
                'name' => $request->name,
                'email' => $request->email,
                'mobile_no' => $request->mobile_no,
                'gender' => $request->gender,
                'city_name' => $request->city_name,
                'dob' => $request->dob,
                'preferred_address' => $request->preferred_address,
                'status' => $request->status,
                'user_id' => Auth::id(),
                'password' => Hash::make(Str::random(8)),
            ]);

            if ($request->preferred_address == 'office') {
                MemberOfficeAddress::create([
                    'member_id' => $member->id,
                    'office_state' => $request->office_state,
                    'office_city' => $request->office_city,
                    'office_pin' => $request->office_pin,
                    'office_address' => $request->office_address,
                    'office_phone' => $request->office_phone,
                    'office_email' => $request->office_email,
                    'office_website' => $request->office_website,
                ]);
            } else {
                MemberResidenceAddress::create([
                    'member_id' => $member->id,
                    'residence_state' => $request->residence_state,
                    'residence_city' => $request->residence_city,
                    'residence_pin' => $request->residence_pin,
                    'residence_address' => $request->residence_address,
                    'residence_phone' => $request->residence_phone,
                    'residence_email' => $request->residence_email,
                    'residence_website' => $request->residence_website,
                ]);
            }
            DB::commit();
            $this->clearMemberCache($member->id);
            return response()->json([
                'status' => 'success',
                'message' => 'Member created successfully!',
                'member_id' => $member->id,
                'redirect_url' => route('manage-member.step2', $member->id)
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong: ' . $e->getMessage()
            ], 500);
        }
    }

    /* UPDATE STEP 1 (EDIT) */
    public function updateStep1(Request $request, $id){
        $member = Member::findOrFail($id);
        $validator = Validator::make($request->all(), [
            'member_type' => 'required|exists:member_types,id',
            'membership_no' => 'required|unique:members,membership_no,' . $id,
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:members,email,' . $id,
            'mobile_no' => 'nullable|string|max:20',
            'gender' => 'nullable|in:male,female,other',
            'city_name' => 'nullable|string|max:255',
            'dob' => 'nullable|date',
            'preferred_address' => 'required|in:office,residence',
            'status' => 'required|in:pending,approved,rejected',
            'office_state' => 'required_if:preferred_address,office|nullable|string|max:255',
            'office_city' => 'required_if:preferred_address,office|nullable|string|max:255',
            'office_pin' => 'nullable|string|max:6',
            'office_address' => 'nullable|string|max:255',
            'office_phone' => 'nullable|string|max:20',
            'office_email' => 'nullable|email',
            'office_website' => 'nullable|url',
            'residence_state' => 'required_if:preferred_address,residence|nullable|string|max:255',
            'residence_city' => 'required_if:preferred_address,residence|nullable|string|max:255',
            'residence_pin' => 'nullable|string|max:6',
            'residence_address' => 'nullable|string|max:255',
            'residence_phone' => 'nullable|string|max:20',
            'residence_email' => 'nullable|email',
            'residence_website' => 'nullable|url',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();
        try {
            $member->update([
                'membership_type_id' => $request->member_type,
                'membership_no' => $request->membership_no,
                'name' => $request->name,
                'email' => $request->email,
                'mobile_no' => $request->mobile_no,
                'gender' => $request->gender,
                'city_name' => $request->city_name,
                'dob' => $request->dob,
                'preferred_address' => $request->preferred_address,
                'status' => $request->status,
            ]);

            if ($request->preferred_address == 'office') {
                MemberOfficeAddress::updateOrCreate(
                    ['member_id' => $member->id],
                    [
                        'office_state' => $request->office_state,
                        'office_city' => $request->office_city,
                        'office_pin' => $request->office_pin,
                        'office_address' => $request->office_address,
                        'office_phone' => $request->office_phone,
                        'office_email' => $request->office_email,
                        'office_website' => $request->office_website,
                    ]
                );
                MemberResidenceAddress::where('member_id', $member->id)->delete();
            } else {
                MemberResidenceAddress::updateOrCreate(
                    ['member_id' => $member->id],
                    [
                        'residence_state' => $request->residence_state,
                        'residence_city' => $request->residence_city,
                        'residence_pin' => $request->residence_pin,
                        'residence_address' => $request->residence_address,
                        'residence_phone' => $request->residence_phone,
                        'residence_email' => $request->residence_email,
                        'residence_website' => $request->residence_website,
                    ]
                );
                MemberOfficeAddress::where('member_id', $member->id)->delete();
            }
            DB::commit();
            $this->clearMemberCache($member->id);
            return response()->json([
                'status' => 'success',
                'message' => 'Member updated successfully!',
                'member_id' => $member->id,
                'redirect_url' => route('manage-member.step2', $member->id)
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong: ' . $e->getMessage()
            ], 500);
        }
    }

    /* STEP 2 - Present Designation Form */
    public function presentAppointmentDesignationForm($id)
    {
        $member = Member::with([
            'officeAddress',
            'residenceAddress',
            'presentDesignations',
            'academicQualifications',
            'trainings'
        ])->findOrFail($id);
        //return response($member);
        return view('backend.pages.member.members.member-registration-form.present-appointment-designation', compact('member'));
    }

    public function storeStep2(Request $request, $id)
    {
        $member = Member::findOrFail($id);
        $rules = [
            'designation' => 'required|string|max:255',
            'institution' => 'required|string|max:255',
            'year_of_joining' => 'required|digits:4|integer|min:1900|max:' . date('Y'),
        ];
        $validator = Validator::make($request->all(), $rules, [
            'designation.required' => 'Designation is required',
            'institution.required' => 'Institution is required',
            'year_of_joining.required' => 'Year of joining is required',
            'year_of_joining.digits' => 'Year must be 4 digits',
            'year_of_joining.min' => 'Year must be at least 1900',
            'year_of_joining.max' => 'Year cannot be in the future',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }
        DB::beginTransaction();
        try {
            MemberPresentDesignation::updateOrCreate(
                ['member_id' => $member->id],
                [
                    'designation' => $request->designation,
                    'institution' => $request->institution,
                    'year_of_joining' => $request->year_of_joining,
                ]
            );
            DB::commit();
            $this->clearMemberCache($member->id);
            return response()->json([
                'status' => 'success',
                'message' => 'Present designation saved successfully!',
                'redirect_url' => route('manage-member.step3', $member->id)
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong: ' . $e->getMessage()
            ], 500);
        }
    }

    public function updateStep2(Request $request, $id)
    {
        $member = Member::findOrFail($id);
        $rules = [
            'designation' => 'required|string|max:255',
            'institution' => 'required|string|max:255',
            'year_of_joining' => 'required|digits:4|integer|min:1900|max:' . date('Y'),
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }
        DB::beginTransaction();
        try {
            MemberPresentDesignation::updateOrCreate(
                ['member_id' => $member->id],
                [
                    'designation' => $request->designation,
                    'institution' => $request->institution,
                    'year_of_joining' => $request->year_of_joining,
                ]
            );
            DB::commit();
            return response()->json([
                'status' => 'success',
                'message' => 'Present designation updated successfully!',
                'redirect_url' => route('manage-member.step3', $member->id) 
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong: ' . $e->getMessage()
            ], 500);
        }
    }

    /* STEP 3 - Academic Qualification */
    public function academicQualificationForm($id)
    {
        $member = Member::with([
            'officeAddress',
            'residenceAddress',
            'presentDesignations',
            'academicQualifications',
            'trainings'
        ])->findOrFail($id);
        return view('backend.pages.member.members.member-registration-form.academic-qualification', compact('member'));
    }

    public function storeStep3(Request $request, $id)
    {
        $member = Member::findOrFail($id);
        $rules = [];
        foreach ($request->qualifications as $index => $qualification) {
            $rules["qualifications.{$index}.degree"] = 'required|string|max:255';
            $rules["qualifications.{$index}.institution"] = 'required|string|max:255';
            $rules["qualifications.{$index}.year_of_passing"] = 'required|digits:4|integer|min:1900|max:' . date('Y');
        }
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }
        DB::beginTransaction();
        try {
            MemberAcademicQualification::where('member_id', $member->id)->delete();
            foreach ($request->qualifications as $qualification) {
                MemberAcademicQualification::create([
                    'member_id' => $member->id,
                    'degree' => $qualification['degree'],
                    'institution' => $qualification['institution'],
                    'year_of_passing' => $qualification['year_of_passing'],
                ]);
            }
            DB::commit();
            $this->clearMemberCache($member->id);
            return response()->json([
                'status' => 'success',
                'message' => 'Academic qualifications saved successfully!',
                'redirect_url' => route('manage-member.step4', $member->id)
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong: ' . $e->getMessage()
            ], 500);
        }
    }

    public function updateStep3(Request $request, $id)
    {
        $member = Member::findOrFail($id);
        $rules = [];
        foreach ($request->qualifications as $index => $qualification) {
            $rules["qualifications.{$index}.degree"] = 'required|string|max:255';
            $rules["qualifications.{$index}.institution"] = 'required|string|max:255';
            $rules["qualifications.{$index}.year_of_passing"] = 'required|digits:4|integer|min:1900|max:' . date('Y');
        }
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }
        DB::beginTransaction();
        try {
            MemberAcademicQualification::where('member_id', $member->id)->delete();
            foreach ($request->qualifications as $qualification) {
                MemberAcademicQualification::create([
                    'member_id' => $member->id,
                    'degree' => $qualification['degree'],
                    'institution' => $qualification['institution'],
                    'year_of_passing' => $qualification['year_of_passing'],
                ]);
            }
            DB::commit();
            $this->clearMemberCache($member->id);
            return response()->json([
                'status' => 'success',
                'message' => 'Academic qualifications updated successfully!',
                'redirect_url' => route('manage-member.step4', $member->id) 
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong: ' . $e->getMessage()
            ], 500);
        }
    }

    /* STEP 4 - Training in Urology */
    public function trainingInUrologyForm($id)
    {
        $member = Member::with([
            'officeAddress',
            'residenceAddress',
            'presentDesignations',
            'academicQualifications',
            'trainings'
        ])->findOrFail($id);
        return view('backend.pages.member.members.member-registration-form.training-in-urology', compact('member'));
    }

    public function storeStep4(Request $request, $id)
    {
        $member = Member::findOrFail($id);
        $rules = [
            'usi_member' => 'required|in:yes,no',
        ];
        if ($request->usi_member == 'yes') {
            $rules['usi_number'] = 'required|string|max:50|unique:members,usi_number,' . $id;
        }
        if ($request->has('trainings')) {
            foreach ($request->trainings as $index => $training) {
                $rules["trainings.{$index}.institution"] = 'required|string|max:255';
                $rules["trainings.{$index}.from_date"] = 'required|date';
                $rules["trainings.{$index}.to_date"] = 'required|date|after:trainings.{$index}.from_date';
            }
        } else {
            $rules['trainings'] = 'required|array|min:1';
        }
        $validator = Validator::make($request->all(), $rules, [
            'trainings.*.institution.required' => 'Institution is required',
            'trainings.*.from_date.required' => 'From date is required',
            'trainings.*.to_date.required' => 'To date is required',
            'trainings.*.to_date.after' => 'To date must be after from date',
            'usi_number.required' => 'USI number is required',
            'usi_number.unique' => 'This USI number already exists',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }
        DB::beginTransaction();       
        try {
            MemberUrologyTraining::where('member_id', $member->id)->delete();
            if ($request->has('trainings')) {
                foreach ($request->trainings as $training) {
                    MemberUrologyTraining::create([
                        'member_id' => $member->id,
                        'institution' => $training['institution'],
                        'from_date' => $training['from_date'],
                        'to_date' => $training['to_date'],
                    ]);
                }
            }
            $updateData = ['usi_member' => $request->usi_member];
            if ($request->usi_member == 'yes') {
                $updateData['usi_number'] = $request->usi_number;
            } else {
                $updateData['usi_number'] = null;
            }
            $member->update($updateData);
            DB::commit();
            $this->clearMemberCache($member->id);
            return response()->json([
                'status' => 'success',
                'message' => 'Trainings saved successfully!',
                'redirect_url' => route('manage-member.index')
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong: ' . $e->getMessage()
            ], 500);
        }
    }

    public function updateStep4(Request $request, $id)
    {
        $member = Member::findOrFail($id);
        $rules = [
            'usi_member' => 'required|in:yes,no',
        ];
        if ($request->usi_member == 'yes') {
            $rules['usi_number'] = 'required|string|max:50|unique:members,usi_number,' . $id;
        }
        if ($request->has('trainings')) {
            foreach ($request->trainings as $index => $training) {
                $rules["trainings.{$index}.institution"] = 'required|string|max:255';
                $rules["trainings.{$index}.from_date"] = 'required|date';
                $rules["trainings.{$index}.to_date"] = 'required|date|after:trainings.{$index}.from_date';
            }
        }
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }
        DB::beginTransaction();
        try {
            MemberUrologyTraining::where('member_id', $member->id)->delete();
            if ($request->has('trainings')) {
                foreach ($request->trainings as $training) {
                    MemberUrologyTraining::create([
                        'member_id' => $member->id,
                        'institution' => $training['institution'],
                        'from_date' => $training['from_date'],
                        'to_date' => $training['to_date'],
                    ]);
                }
            }
            $updateData = ['usi_member' => $request->usi_member];
            if ($request->usi_member == 'yes') {
                $updateData['usi_number'] = $request->usi_number;
            } else {
                $updateData['usi_number'] = null;
            }
            $member->update($updateData);
            DB::commit();
            $this->clearMemberCache($member->id);
            return response()->json([
                'status' => 'success',
                'message' => 'Trainings updated successfully!',
                'redirect_url' => route('manage-member.index') 
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong: ' . $e->getMessage()
            ], 500);
        }
    }

    public function importIndex()
    {
        return view('backend.pages.member.members.import.index');
    }

    public function importStore(Request $request)
    {
        $request->validate([
            'excel_file' => 'required|mimes:xlsx,xls,csv'
        ], [
            'excel_file.required' => 'Please upload a file',
            'excel_file.mimes' => 'Only Excel or CSV files allowed',
        ]);
        try {
            $import = new MembersImport(Auth::id());
            Excel::queueImport($import, $request->file('excel_file'));
            if ($import->failures()->isNotEmpty()) {
                $errors = [];
                foreach ($import->failures() as $failure) {
                    $errors[] = "Row {$failure->row()}: " . implode(', ', $failure->errors());
                }
                return response()->json([
                    'status' => 'error',
                    'import_errors' => $errors
                ]);
            }
            return response()->json([
                'status' => 'success',
                'message' => 'Members imported successfully!',
                'route_redirect' => route('manage-member.index')
            ]);
        } catch (\Exception $e) {
            Log::error('Import Errors', $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function show($id){
        $member = Member::with([
            'type',
            'officeAddress',
            'residenceAddress',
            'presentDesignations',
            'academicQualifications',
            'trainings',
            'user'
        ])->findOrFail($id);
        
        return view('backend.pages.member.members.show', compact('member'));
    }

    private function clearMemberCache($memberId = null)
    {
        if ($memberId) {
            Cache::forget('member_profile_' . $memberId);
            Cache::forget('member_address_' . $memberId);
        }
        Cache::forget('members_list');
    }
}
