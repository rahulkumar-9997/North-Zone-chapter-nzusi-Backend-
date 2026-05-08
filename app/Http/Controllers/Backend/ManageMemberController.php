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
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class ManageMemberController extends Controller
{
    public function index(Request $request)
    {
        $query = Member::with([
            'type',
            'officeAddress',
            'residenceAddress',
            'presentDesignations',
            'academicQualifications'
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
        $member_lists = $query->orderBy('id', 'desc')->paginate(30);
        if ($request->ajax()) {
            return view('backend.pages.member.members.partials.members-list', compact('member_lists'))->render();
        }
        $members_type = MemberType::select('id', 'title')->get();
        return view('backend.pages.member.members.index', compact('member_lists', 'members_type'));
    }

    public function create()
    {
        $memberTypes = MemberType::select('id', 'title')
        ->where('status', 1)
        ->orderBy('id', 'desc')
        ->get();
        //return response()->json($MemberType);
        return view('backend.pages.member.members.member-registration-form.personal', compact('memberTypes'));
    }

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
            'office_state' => 'nullable|string|max:255',
            'office_city' => 'nullable|string|max:255',
            'office_pin' => 'nullable|string|max:20',
            'office_address' => 'nullable|string',
            'office_phone' => 'nullable|string|max:20',
            'office_email' => 'nullable|email',
            'office_website' => 'nullable|url',
            'residence_state' => 'nullable|string|max:255',
            'residence_city' => 'nullable|string|max:255',
            'residence_pin' => 'nullable|string|max:20',
            'residence_address' => 'nullable|string',
            'residence_phone' => 'nullable|string|max:20',
            'residence_email' => 'nullable|email',
            'residence_website' => 'nullable|url',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();
        
        try {
            // Create Member
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
                'registration_step' => 2,
                'password' => bcrypt('temp123'), // You can generate random password
            ]);

            // Save Office Address if preferred or if data exists
            if ($request->preferred_address == 'office' || $request->office_address || $request->office_city) {
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
            }

            // Save Residence Address if preferred or if data exists
            if ($request->preferred_address == 'residence' || $request->residence_address || $request->residence_city) {
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

            return response()->json([
                'success' => true,
                'message' => 'Member created successfully!',
                'member_id' => $member->id,
                'redirect_url' => route('manage-member.step2', $member->id)
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong: ' . $e->getMessage()
            ], 500);
        }
    }


    public function importIndex()
    {
        /*
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('member_academic_qualifications')->truncate();
        DB::table('member_office_addresses')->truncate();
        DB::table('member_present_designations')->truncate();
        DB::table('member_residence_addresses')->truncate();
        DB::table('members')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        */

        return view('backend.pages.member.members.import.index');
    }

    public function importStore(Request $request)
    {
        /*
            DELETE FROM member_academic_qualifications;
            DELETE FROM members;
         */
        $request->validate([
            'excel_file' => 'required|mimes:xlsx,xls,csv'
        ], [
            'excel_file.required' => 'Please upload a file',
            'excel_file.mimes' => 'Only Excel or CSV files allowed',
        ]);
        try {
            $import = new MembersImport(Auth::id());
            Log::info('Import class loaded: ' . get_class($import));
            Log::info('Import failures', [
                'failures' => $import->failures()
            ]);
            //Excel::import($import, $request->file('excel_file'));
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

    
    
}
