<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Imports\MembersImport;
use Illuminate\Http\Request;
use App\Models\Member;
use App\Models\MemberType;
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
        return view('backend.pages.member.members.create', compact('memberTypes'));
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
