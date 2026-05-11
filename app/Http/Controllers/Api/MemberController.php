<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Member;
use App\Models\MemberOfficeAddress;
use App\Models\MemberResidenceAddress;
use App\Models\MemberAcademicQualification;
use App\Models\MemberPresentDesignation;
use App\Models\MemberUrologyTraining;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use App\Helpers\ImageHelper;
class MemberController extends Controller
{
   
    public function profile(Request $request)
    {
        try {
            $user = $request->user();            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found.',
                    'data' => null
                ], 404);
            }            
            $user->load([
                'presentDesignations',
                'academicQualifications',
                'trainings'
            ]);
            $cacheKey = 'member_profile_' . $user->id;
            $data = Cache::remember($cacheKey, 3600, function () use ($user) {
                return [
                    'id' => $user->id,
                    'membership_no' => $user->membership_no,
                    'name' => $user->name,
                    'email' => $user->email,
                    'gender' => $user->gender,
                    'city_name' => $user->city_name,
                    'mobile_no' => $user->mobile_no,
                    'membership_type_id' => $user->membership_type_id,
                    'dob' => $user->dob ? $user->dob->format('Y-m-d') : null,
                    'usi_member' => $user->usi_member,
                    'usi_number' => $user->usi_number,
                    'preferred_address' => $user->preferred_address,
                    'membership_approved_date' => $user->membership_approved_date ? $user->membership_approved_date->format('Y-m-d') : null,
                    'status' => $user->status,
                    //'user_id' => $user->user_id,
                    'login_attempts' => $user->login_attempts,
                    'last_login_at' => $user->last_login_at ? $user->last_login_at->format('Y-m-d H:i:s') : null,
                    'last_login_ip' => $user->last_login_ip,
                    'is_active' => $user->is_active,
                    'is_verified' => $user->is_verified,
                    'password_changed_at' => $user->password_changed_at ? $user->password_changed_at->format('Y-m-d H:i:s') : null,
                    'designation_status' => $user->presentDesignations->isNotEmpty() ? 'done' : 'pending',
                    'academic_status' => $user->academicQualifications->isNotEmpty() ? 'done' : 'pending',
                    'training_status' => $user->trainings->isNotEmpty() ? 'done' : 'pending',
                ];
            });
            
            return response()->json([
                'success' => true,
                'message' => 'Profile data fetched successfully.',
                'data' => $data
            ], 200);
            
        } catch (\Exception $e) {
            Log::error('Profile fetch error: ' . $e->getMessage(), [
                'user_id' => $request->user()?->id
            ]);            
            return response()->json([
                'success' => false,
                'message' => 'Unable to fetch profile. Please try again later.',
                'data' => null
            ], 500);
        }
    }    

    public function updateProfile(Request $request)
    {
        try {
            $user = $request->user();
            $rules = [
                'name' => 'required|string|max:255',
                'email' => 'required|email|max:255|unique:members,email,' . $user->id,
                'gender' => 'required|in:male,female,other',
                'city_name' => 'required|string|max:255',
                'mobile_no' => 'required|string|max:20',
                'dob' => 'required|date|before:today',
            ];
            $messages = [
                'dob.before' => 'Date of birth must be before today.',
            ];
            $validatedData = $request->validate($rules, $messages);
            $updateData = [
                'name' => $validatedData['name'],
                'email' => $validatedData['email'],
                'gender' => $validatedData['gender'],
                'city_name' => $validatedData['city_name'],
                'mobile_no' => $validatedData['mobile_no'],
                'dob' => $validatedData['dob'],
            ];
            $user->update($updateData);
            Cache::forget('member_profile_' . $user->id);
            Cache::forget('member_address_' . $user->id);
            $user->refresh();
            return response()->json([
                'success' => true,
                'message' => 'Profile updated successfully.',
                'data' => [
                    'id' => $user->id,
                    'membership_no' => $user->membership_no,
                    'name' => $user->name,
                    'email' => $user->email,
                    'gender' => $user->gender,
                    'city_name' => $user->city_name,
                    'mobile_no' => $user->mobile_no,
                    'dob' => $user->dob ? $user->dob->format('Y-m-d') : null,
                    'preferred_address' => $user->preferred_address,
                    'status' => $user->status,
                    'is_active' => $user->is_active,
                    'is_verified' => $user->is_verified,
                ]
            ], 200);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            Log::error('Profile update error: ' . $e->getMessage(), [
                'user_id' => $request->user()?->id
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Unable to update profile.',
                'data' => null
            ], 500);
        }
    }

    public function getAddress(Request $request)
    {
        try {
            $user = $request->user();            
            $cacheKey = 'member_address_' . $user->id;            
            $data = Cache::remember($cacheKey, 3600, function () use ($user) {
                $user->load(['officeAddress', 'residenceAddress']);                
                return [
                    'preferred_address' => $user->preferred_address,
                    'office_address' => $user->officeAddress ? [
                        'state' => $user->officeAddress->office_state,
                        'city' => $user->officeAddress->office_city,
                        'pin' => $user->officeAddress->office_pin,
                        'address' => $user->officeAddress->office_address,
                        'phone' => $user->officeAddress->office_phone,
                        'email' => $user->officeAddress->office_email,
                        'website' => $user->officeAddress->office_website,
                    ] : null,
                    'residence_address' => $user->residenceAddress ? [
                        'state' => $user->residenceAddress->residence_state,
                        'city' => $user->residenceAddress->residence_city,
                        'pin' => $user->residenceAddress->residence_pin,
                        'address' => $user->residenceAddress->residence_address,
                        'phone' => $user->residenceAddress->residence_phone,
                        'email' => $user->residenceAddress->residence_email,
                        'website' => $user->residenceAddress->residence_website,
                    ] : null,
                ];
            });
            
            return response()->json([
                'success' => true,
                'message' => 'Address fetched successfully.',
                'data' => $data
            ], 200);
            
        } catch (\Exception $e) {
            Log::error('Get address error: ' . $e->getMessage());            
            return response()->json([
                'success' => false,
                'message' => 'Unable to fetch address.',
                'data' => null
            ], 500);
        }
    }

    public function updateAddress(Request $request)
    {
        try {
            $user = $request->user();            
            $rules = [
                'preferred_address' => 'required|in:office,residence',                
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
            ];            
            $messages = [
                'preferred_address.required' => 'Preferred address is required.',
                'office_state.required_if' => 'Office state is required.',
                'office_city.required_if' => 'Office city is required.',
                'residence_state.required_if' => 'Residence state is required.',
                'residence_city.required_if' => 'Residence city is required.',
            ];            
            $request->validate($rules, $messages);
            $user->update([
                'preferred_address' => $request->preferred_address
            ]);
            if ($request->preferred_address == 'office') {
                MemberOfficeAddress::updateOrCreate(
                    ['member_id' => $user->id],
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
                MemberResidenceAddress::where('member_id', $user->id)->delete();
            } else {
                MemberResidenceAddress::updateOrCreate(
                    ['member_id' => $user->id],
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
                MemberOfficeAddress::where('member_id', $user->id)->delete();
            }
            Cache::forget('member_profile_' . $user->id);
            Cache::forget('member_address_' . $user->id);
            $user->refresh();
            $user->load(['officeAddress', 'residenceAddress']);
            return response()->json([
                'success' => true,
                'message' => 'Address updated successfully.',
            ], 200);
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $e->errors()
            ], 422);
            
        } catch (\Exception $e) {
            Log::error('Update address error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Unable to update address.',
                'data' => null
            ], 500);
        }
    }

    public function getPresentAppointmentDesignation(Request $request)
    {
        try {
            $user = $request->user();
            $designation = $user->presentDesignations()->first();            
            return response()->json([
                'success' => true,
                'message' => 'Designation fetched successfully.',
                'data' => $designation ? [
                    'id' => $designation->id,
                    'designation' => $designation->designation,
                    'institution' => $designation->institution,
                    'year_of_joining' => $designation->year_of_joining,
                ] : null
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Unable to fetch designation.',
                'data' => null
            ], 500);
        }
    }

    public function updatePresentAppointmentDesignation(Request $request)
    {
        try {
            $user = $request->user();
            $rules = [
                'designation' => 'required|string|max:255',
                'institution' => 'required|string|max:255',
                'year_of_joining' => 'required|integer|digits:4|min:1900|max:' . date('Y'),
            ];
            $messages = [
                'designation.required' => 'Designation field is required.',
                'designation.string' => 'Designation must be a valid string.',
                'designation.max' => 'Designation may not be greater than 255 characters.',
                'institution.required' => 'Institution field is required.',
                'institution.string' => 'Institution must be a valid string.',
                'institution.max' => 'Institution may not be greater than 255 characters.',

                'year_of_joining.required' => 'Year of joining field is required.',
                'year_of_joining.integer' => 'Year of joining must be a number.',
                'year_of_joining.digits' => 'Year of joining must be 4 digits.',
                'year_of_joining.min' => 'Year of joining must be after 1900.',
                'year_of_joining.max' => 'Year of joining cannot be greater than current year.',
            ];
            $validatedData = $request->validate($rules, $messages);
            $designation = MemberPresentDesignation::updateOrCreate(
                ['member_id' => $user->id],
                [
                    'designation' => $validatedData['designation'],
                    'institution' => $validatedData['institution'],
                    'year_of_joining' => $validatedData['year_of_joining'],
                ]
            );
            return response()->json([
                'success' => true,
                'message' => 'Designation updated successfully.',
                'data' => [
                    'id' => $designation->id,
                    'designation' => $designation->designation,
                    'institution' => $designation->institution,
                    'year_of_joining' => $designation->year_of_joining,
                ]
            ], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            Log::error('Designation update error: ' . $e->getMessage(), [
                'user_id' => $request->user()?->id
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong. Please try again later.',
                'data' => null
            ], 500);
        }
    }

    public function getAcademicQualification(Request $request)
    {
        try {
            $user = $request->user();
            $qualifications = $user->academicQualifications;            
            return response()->json([
                'success' => true,
                'message' => 'Academic Qualifications fetched successfully.',
                'data' => $qualifications->map(function ($q) {
                    return [
                        'id' => $q->id,
                        'degree' => $q->degree,
                        'institution' => $q->institution,
                        'year_of_passing' => $q->year_of_passing,
                    ];
                })
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Unable to fetch Academic qualifications.',
                'data' => null
            ], 500);
        }
    }

    public function updateAcademicQualification(Request $request)
    {
        try {
            $user = $request->user();
            $rules = [
                'qualifications' => 'required|array|min:1',
                'qualifications.*.degree' => 'required|string|max:255',
                'qualifications.*.institution' => 'required|string|max:255',
                'qualifications.*.year_of_passing' => 'required|integer|digits:4|min:1900|max:' . date('Y'),
            ];
            $messages = [
                'qualifications.required' => 'Qualifications field is required.',
                'qualifications.array' => 'Qualifications must be an array.',
                'qualifications.min' => 'At least one qualification is required.',

                'qualifications.*.degree.required' => 'Degree field is required.',
                'qualifications.*.degree.string' => 'Degree must be a valid string.',
                'qualifications.*.degree.max' => 'Degree may not be greater than 255 characters.',

                'qualifications.*.institution.required' => 'Institution field is required.',
                'qualifications.*.institution.string' => 'Institution must be a valid string.',
                'qualifications.*.institution.max' => 'Institution may not be greater than 255 characters.',

                'qualifications.*.year_of_passing.required' => 'Year of passing field is required.',
                'qualifications.*.year_of_passing.integer' => 'Year of passing must be a number.',
                'qualifications.*.year_of_passing.digits' => 'Year of passing must be 4 digits.',
                'qualifications.*.year_of_passing.min' => 'Year of passing must be after 1900.',
                'qualifications.*.year_of_passing.max' => 'Year of passing cannot be greater than current year.',
            ];
            $validatedData = $request->validate($rules, $messages);
            MemberAcademicQualification::where('member_id', $user->id)->delete();
            $newQualifications = [];
            foreach ($validatedData['qualifications'] as $qualification) {
                $newQualifications[] = MemberAcademicQualification::create([
                    'member_id' => $user->id,
                    'degree' => $qualification['degree'],
                    'institution' => $qualification['institution'],
                    'year_of_passing' => $qualification['year_of_passing'],
                ]);
            }
            return response()->json([
                'success' => true,
                'message' => 'Qualifications updated successfully.',
                // 'data' => collect($newQualifications)->map(function ($q) {
                //     return [
                //         'id' => $q->id,
                //         'degree' => $q->degree,
                //         'institution' => $q->institution,
                //         'year_of_passing' => $q->year_of_passing,
                //     ];
                // })
            ], 200);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $e->errors(),
            ], 422);

        } catch (\Exception $e) {
            Log::error('Academic qualification update error: ' . $e->getMessage(), [
                'user_id' => $request->user()?->id
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Unable to update qualifications.',
                'data' => null
            ], 500);
        }
    }

    public function getTrainingInUrology(Request $request)
    {
        try {
            $user = $request->user();
            $trainings = $user->trainings;            
            return response()->json([
                'success' => true,
                'message' => 'Trainings fetched successfully.',
                'data' => [
                    $trainings->map(function ($t) {
                        return [
                            'id' => $t->id,
                            'institution' => $t->institution,
                            'from_date' => $t->from_date ? $t->from_date->format('Y-m-d') : null,
                            'to_date' => $t->to_date ? $t->to_date->format('Y-m-d') : null,
                        ];
                    })
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Unable to fetch trainings.',
                'data' => null
            ], 500);
        }
    }

    public function updateTrainingInUrology(Request $request)
    {
        try {
            $user = $request->user();
            $rules = [
                'trainings' => 'required|array|min:1',
                'trainings.*.institution' => 'required|string|max:255',
                'trainings.*.from_date' => 'required|date',
                'trainings.*.to_date' => 'required|date|after:trainings.*.from_date',
            ];
            $messages = [
                'trainings.required' => 'Trainings field is required.',
                'trainings.array' => 'Trainings must be an array.',
                'trainings.min' => 'At least one training is required.',

                'trainings.*.institution.required' => 'Institution field is required.',
                'trainings.*.institution.string' => 'Institution must be a valid string.',
                'trainings.*.institution.max' => 'Institution may not be greater than 255 characters.',

                'trainings.*.from_date.required' => 'From date field is required.',
                'trainings.*.from_date.date' => 'From date must be a valid date.',

                'trainings.*.to_date.required' => 'To date field is required.',
                'trainings.*.to_date.date' => 'To date must be a valid date.',
                'trainings.*.to_date.after' => 'To date must be after from date.',
            ];
            $validatedData = $request->validate($rules, $messages);
            MemberUrologyTraining::where('member_id', $user->id)->delete();
            foreach ($validatedData['trainings'] as $training) {
                MemberUrologyTraining::create([
                    'member_id' => $user->id,
                    'institution' => $training['institution'],
                    'from_date' => $training['from_date'],
                    'to_date' => $training['to_date'],
                ]);
            }
            return response()->json([
                'success' => true,
                'message' => 'Trainings updated successfully.'
            ], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            Log::error('Training update error: ' . $e->getMessage(), [
                'user_id' => $request->user()?->id
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Unable to update trainings.',
                'data' => null
            ], 500);
        }
    }
    
    public function logout(Request $request)
    {
        try {
            $user = $request->user();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized.'
                ], 401);
            }
            $user->currentAccessToken()->delete();
            return response()->json([
                'success' => true,
                'message' => 'Logged out successfully.'
            ], 200);
        } catch (\Exception $e) {
            Log::error('Logout error: ' . $e->getMessage(), [
                'user_id' => $request->user()?->id
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Unable to logout.',
                'data' => null
            ], 500);
        }
    }
    
    public function logoutAll(Request $request)
    {
        $request->user()->tokens()->delete();
        return response()->json([
            'success' => true,
            'message' => 'Logged out from all devices'
        ]);
    }
}