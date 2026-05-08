<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use App\Mail\SendOtpMail;
use App\Models\Member;
use Illuminate\Support\Facades\Mail;

class MemberAuthController extends Controller
{
    public function loginOrCreateAccountWithOtp(Request $request)
    {
        return $this->sendOtp($request); 
    }

    public function sendOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'contact' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation Error',
                'errors' => $validator->errors()
            ], 422);
        }

        $input = trim($request->contact);
        $isEmail = filter_var($input, FILTER_VALIDATE_EMAIL);
        $isMobileNo = preg_match('/^[6-9]\d{9}$/', $input);
        $member = null;
        $field = '';        
        if ($isEmail) {
            $member = Member::where('email', $input)->first();
            $field = 'email';
        } elseif ($isMobileNo) {
            $member = Member::where('mobile_no', $input)->first();
            $field = 'mobile_no';
        } else {
            $member = Member::where('membership_no', $input)->first();
            $field = 'membership_no';
        }        
        $isNewUser = false;        
        if (!$member) {
            return response()->json([
                'success' => false,
                'message' => 'Member not found'
            ], 404);
        }
        if($member->status == "pending"){
            return response()->json([
                'success' => false,
                'message' => 'Your Account is pending'
            ], 403);
        }
        elseif($member->status == "rejected"){
            return response()->json([
                'success' => false,
                'message' => 'Your Account is rejected'
            ], 403);
        }
        elseif($member->is_active==0){
            return response()->json([
                'success' => false,
                'message' => 'Your Account is deactivated'
            ], 403);
        }
        elseif($member->is_verified==0){
            return response()->json([
                'success' => false,
                'message' => 'Your Account is not verified!'
            ], 403);
        }  

        $otp = random_int(100000, 999999);
        cache()->put('otp_' . $member->id, $otp, now()->addMinutes(5));
        cache()->put('otp_sent_' . $member->id, now(), now()->addMinutes(1));
        if ($member->email) {
            $this->sendEmailOtp($member->email, $otp);
        }        
        if ($member->mobile_no) {
            $this->sendSmsOtp($member->mobile_no, $otp);
        }        
        Log::info("OTP for {$input}: {$otp}");        
        return response()->json([
            'success' => true,
            'message' => 'OTP sent successfully',
            'data' => [
                'member_id' => $member->id,
                'contact' => $input,
                'is_new_user' => $isNewUser,
            ]
        ]);
    }
    
    public function verifyOtpAndLogin(Request $request)
    {       
        $validator = Validator::make($request->all(), [
            'contact' => 'required|string',
            'otp' => 'required|digits:6'
        ]);      
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation Error',
                'errors' => $validator->errors()
            ], 422);
        }        
        $input = trim($request->contact);
        $isEmail = filter_var($input, FILTER_VALIDATE_EMAIL);
        $isMobileNo = preg_match('/^[6-9]\d{9}$/', $input);
        $member = null;
        
        if ($isEmail) {
            $member = Member::where('email', $input)->first();
        } elseif ($isMobileNo) {
            $member = Member::where('mobile_no', $input)->first();
        } else {
            $member = Member::where('membership_no', $input)->first();
        }
        
        if (!$member) {
            return response()->json([
                'success' => false,
                'message' => 'Member not found'
            ], 404);
        }
        
        if ($member->status != 'approved') {
            return response()->json([
                'success' => false,
                'message' => 'Your account is ' . $member->status
            ], 403);
        }        
        
        if ($member->is_active != 1) {
            return response()->json([
                'success' => false,
                'message' => 'Your Account is deactivated'
            ], 403);
        }
        
        if ($member->is_verified != 1) {
            return response()->json([
                'success' => false,
                'message' => 'Your Account is not verified!'
            ], 403);
        }
        
        if ($member->login_attempts >= 5) {
            return response()->json([
                'success' => false,
                'message' => 'Too many attempts. Try again later.'
            ], 429);
        }
        
        $cachedOtp = cache()->get('otp_' . $member->id);
        if (!$cachedOtp || $cachedOtp != $request->otp) {            
            $member->increment('login_attempts');
            return response()->json([
                'success' => false,
                'message' => 'Invalid or expired OTP'
            ], 401);
        }
        $member->update([
            'last_login_at' => now(),
            'login_attempts' => 0
        ]); 
        cache()->forget('otp_' . $member->id);        
        $isProfileIncomplete = empty($member->name) || empty($member->email);
        $member->tokens()->delete();
        $tokenResult = $member->createToken('auth_token');
        $plainTextToken = $tokenResult->plainTextToken;  
        return response()->json([
            'success' => true,
            'message' => 'Login successful',
            'data' => [
                'member' => $member,
                'access_token' => $plainTextToken,
                'token_type' => 'Bearer',
                'is_profile_complete' => !$isProfileIncomplete,
                'expires_in' => 12 * 60 * 60
            ]
        ]);
    }

    public function resendOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'contact' => 'required|string',
        ]);        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation Error',
                'errors' => $validator->errors()
            ], 422);
        }        
        $input = trim($request->contact);
        $isEmail = filter_var($input, FILTER_VALIDATE_EMAIL);
        $isMobileNo = preg_match('/^[6-9]\d{9}$/', $input);
        $member = null;        
        if ($isEmail) {
            $member = Member::where('email', $input)->first();
        } elseif ($isMobileNo) {
            $member = Member::where('mobile_no', $input)->first();
        } else {
            $member = Member::where('membership_no', $input)->first();
        }
        
        if (!$member) {
            return response()->json([
                'success' => false,
                'message' => 'Member not found'
            ], 404);
        }
        if ($member->status != 'approved') {
            return response()->json([
                'success' => false,
                'message' => 'Your account is ' . $member->status
            ], 403);
        }
        
        if ($member->is_active != 1) {
            return response()->json([
                'success' => false,
                'message' => 'Your Account is deactivated'
            ], 403);
        }
        
        if ($member->is_verified != 1) {
            return response()->json([
                'success' => false,
                'message' => 'Your Account is not verified!'
            ], 403);
        }
        
        
        $lastOtpTime = cache()->get('otp_sent_' . $member->id);
        if ($lastOtpTime && now()->diffInSeconds($lastOtpTime) < 60) {
            $waitTime = 60 - now()->diffInSeconds($lastOtpTime);
            return response()->json([
                'success' => false,
                'message' => "Please wait {$waitTime} seconds before retrying",
                'data' => ['wait_time' => $waitTime]
            ], 429);
        }
        
        $otp = random_int(100000, 999999);        
        cache()->put('otp_' . $member->id, $otp, now()->addMinutes(5));
        cache()->put('otp_sent_' . $member->id, now(), now()->addMinutes(1));        
        if ($member->email) {
            $this->sendEmailOtp($member->email, $otp);
        }        
        if ($member->mobile_no) {
            $this->sendSmsOtp($member->mobile_no, $otp);
        }
        if (app()->environment('local')) {
            Log::info("Resent OTP for {$input}: {$otp}");
        }        
        return response()->json([
            'success' => true,
            'message' => 'OTP resent successfully',
            'data' => [
                'member_id' => $member->id,
                'contact' => $input,
                'otp' => app()->environment('local') ? $otp : null
            ]
        ]);
    }
    
    private function sendEmailOtp($email, $otp)
    {
        try {
            Mail::to($email)->send(new SendOtpMail($otp));
            Log::info("OTP email sent to {$email}");
        } catch (\Exception $e) {
            Log::error("Email sending failed: " . $e->getMessage());
        }
    }
    
    private function sendSmsOtp($phone, $otp)
    {
        // Implement your SMS gateway here
        Log::info("SMS OTP for {$phone}: {$otp}");
    }
    
    public function checkContactExists(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'contact' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation Error',
                'errors' => $validator->errors()
            ], 422);
        }

        $input = trim($request->contact);
        $isEmail = filter_var($input, FILTER_VALIDATE_EMAIL);
        $isMobileNo = preg_match('/^[6-9]\d{9}$/', $input);
        
        $exists = false;
        $contactType = '';        
        if ($isEmail) {
            $exists = Member::where('email', $input)->exists();
            $contactType = 'email';
        } elseif ($isMobileNo) {
            $exists = Member::where('mobile_no', $input)->exists();
            $contactType = 'mobile';
        } else {
            $exists = Member::where('membership_no', $input)->exists();
            $contactType = 'membership_no';
        }
        
        return response()->json([
            'success' => true,
            'data' => [
                'exists' => $exists,
                'contact_type' => $contactType
            ]
        ]);
    }
}