<?php
namespace App\Http\Controllers\Backend;
namespace App\Http\Controllers\Backend;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Blog;
use App\Models\BlogCategory;
use App\Models\MemberType;
use App\Models\Member;
use App\Models\Label;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
class DashboardController extends Controller
{
    public function index(){
        $memberCounts = Member::selectRaw('status, COUNT(*) as count')
        ->groupBy('status')
        ->pluck('count', 'status');
        $data = [
            'label' => Label::count(),
            'blog' => Blog::count(),
            'BlogCategory' => BlogCategory::count(),
            'MemberType' => MemberType::count(),
            'member_total' => $memberCounts->sum(),
            'member_approved' => $memberCounts['approved'] ?? 0,
            'member_pending' => $memberCounts['pending'] ?? 0,
            'member_rejected' => $memberCounts['rejected'] ?? 0,
        ];     
        return view('backend.pages.dashboard.index', compact('data'));
    }

    
    public function showProfileUpdateForm(){
        $user = Auth::user();
        return view('backend.profile.index' , compact('user'));
    }

    public function updateProfile(Request $request){
        $user_id = Auth::user()->id;
        
        // $this->validate($request, [
        //     'profile_name' => ['nullable', 'required'],
        //     'mobile_number' =>  ['nullable', 'required|numeric|digits:10'],
        //     //'profile_photo' =>  ['nullable', 'required'],
        //     'update_password' =>  ['nullable', 'required|digits:8'],
        // ]);

        $input['name'] = $request->input('profile_name');
        $input['phone_number'] = $request->input('mobile_number');
        $input['email'] = $request->input('profile_email');
       
        $user_row = User::find($user_id);
        
        if ($request->hasFile('profile_photo')){
            $image = $request->file('profile_photo');
            $filenameWithExt = $image->getClientOriginalName();  
            $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
            $extension = $image->getClientOriginalExtension();
            $image_file_name = $filename.'_'.time().'.'.$extension;
            
           
            $destination_path_main_img_ = public_path('hotel-sankalp-image-file/profile-img/');
            /*Unlink image*/
            // $file_old_thumb = $destination_path_thumb.$user_row->profile_img;
            if(!empty($user_row->profile_img)){
                $file_old_main = $destination_path_main_img_.$user_row->profile_img;
                
                if (file_exists($file_old_main)) {
                    unlink($file_old_main);
                }
            }
            $destinationPath = public_path('hotel-sankalp-image-file/profile-img/');
            $image->move($destinationPath, $image_file_name);
            $input['profile_img'] = $image_file_name;
        }
        $image_upload = $user_row->update($input);
        if($request->input('current_password') && $request->input('new_password')){
            $auth = Auth::user();
            if (!Hash::check($request->get('current_password'), $auth->password)) 
            {
                return back()->with('error', "Current Password is Invalid");
            }
                        
            if (strcmp($request->get('current_password'), $request->new_password) == 0) 
            {
                return redirect()->back()->with("error", "New Password cannot be same as your current password.");
            }
            $user =  User::find($auth->id);
            $user->password =  Hash::make($request->new_password);
            $user->save();
            return back()->with('success', "Password Changed Successfully");
        }
 

        if ($image_upload){
            return redirect('manage-profile')->with('success','Profile updated successfully');
        }else{
            return redirect()->back()->with('error','Somthings went wrong please try again !.');
        }
    }

    public function memberAnalytics(Request $request)
    {
        $year = $request->year ?? date('Y');
        $data = Member::select(
                DB::raw('MONTH(created_at) as month'),
                DB::raw('COUNT(*) as total'),
                DB::raw("SUM(CASE WHEN status = 'approved' THEN 1 ELSE 0 END) as approved"),
                DB::raw("SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending"),
                DB::raw("SUM(CASE WHEN status = 'rejected' THEN 1 ELSE 0 END) as rejected")
            )
            ->whereYear('created_at', $year)
            ->groupBy(DB::raw('MONTH(created_at)'))
            ->orderBy('month')
            ->get();
        $months = [];
        for ($i = 1; $i <= 12; $i++) {
            $months[$i] = [
                'month' => date('M', mktime(0, 0, 0, $i, 1)),
                'total' => 0,
                'approved' => 0,
                'pending' => 0,
                'rejected' => 0,
            ];
        }

        foreach ($data as $row) {
            $months[$row->month] = [
                'month' => date('M', mktime(0, 0, 0, $row->month, 1)),
                'total' => $row->total,
                'approved' => $row->approved,
                'pending' => $row->pending,
                'rejected' => $row->rejected,
            ];
        }

        return response()->json(array_values($months));
    }
}
