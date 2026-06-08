<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Mail\AbstractSubmissionMail;
use App\Mail\AbstractSubmissionMailUser;
use App\Models\AbstractSubmission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class AbstractSubmissionController extends Controller
{
    public function abstractSubmissionStore(Request $request)
    {
        DB::beginTransaction();
        try {
            $validator = Validator::make(
                $request->all(),
                [
                    'first_name' => 'required|string|max:255',
                    'last_name' => 'nullable|string|max:255',
                    'email' => 'nullable|email|max:255',
                    'phone' => [
                        'nullable',
                        'regex:/^[0-9+\-\s()]+$/',
                        'max:20'
                    ],
                    'institution' => 'nullable|string|max:255',
                    'designation' => 'nullable|string|max:255',
                    'city' => 'nullable|string|max:255',
                    'presentation_type' => 'nullable|string|max:255',
                    'topic_category' => 'nullable|string|max:255',
                    'abstract_title' => 'nullable|string|max:500',
                    'authors' => 'nullable|string',
                    'corresponding_author' => 'nullable|string|max:255',
                    'abstract_body' => 'nullable|string|max:5000',
                    'supporting_file' => [
                        'nullable',
                        'file',
                        'mimes:pdf',
                        'max:51200',// 50MB
                    ],
                    'nzusi_membership_no' => 'nullable|string|max:255',
                    'usi_membership_no' => 'nullable|string|max:255',
                    'conf_reg_no' => 'nullable|string|max:255',
                    'video_link' => 'nullable|string|max:255',
                ],
                [
                    'first_name.required' => 'First name is required.',
                    'last_name.required' => 'Last name is required.',
                    'email.required' => 'Email is required.',
                    'email.email' => 'Please enter valid email.',
                    'institution.required' => 'Institution is required.',
                    'designation.required' => 'Designation is required.',
                    'presentation_type.required' => 'Presentation type is required.',
                    'topic_category.required' => 'Topic category is required.',
                    'abstract_title.required' => 'Abstract title is required.',
                    'authors.required' => 'Authors field is required.',
                    'corresponding_author.required' => 'Corresponding author is required.',
                    'abstract_body.required' => 'Abstract body is required.',
                    'supporting_file.max' => 'File size should not exceed 50 MB.',
                    'nzusi_membership_no.max' => 'NZUSI membership number should not exceed 255 characters.',
                    'usi_membership_no.max' => 'USI membership number should not exceed 255 characters.',
                    'conf_reg_no.max' => 'Conference registration number should not exceed 255 characters.',
                    'video_link.max' => 'Video link should not exceed 255 characters.',
                ]
            );
            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validation failed.',
                    'errors' => $validator->errors(),
                ], 422);
            }
            $fileName = null;
            $absoluteFilePath = null;
            if ($request->hasFile('supporting_file')) {
                $file = $request->file('supporting_file');
                $extension = $file->getClientOriginalExtension();
                $originalName = pathinfo(
                    $file->getClientOriginalName(),
                    PATHINFO_FILENAME
                );
                $fileName =
                    time() . '_' .
                    Str::slug($originalName) . '.' .
                    $extension;

                Storage::disk('public')->putFileAs(
                    'images/abstract-submission',
                    $file,
                    $fileName
                );
                $absoluteFilePath = storage_path(
                    'app/public/images/abstract-submission/' . $fileName
                );
            }
            /* Generate Abstract ID */
            $firstLetter = strtoupper(substr(trim($request->first_name), 0, 1));
            $presentationLetter = strtoupper(
                substr(trim($request->presentation_type), 0, 1)
            );
            $topicLetter = strtoupper(
                substr(trim($request->topic_category), 0, 1)
            );
            $titleLetter = strtoupper(
                substr(trim($request->abstract_title), 0, 1)
            );
            $randomString = strtoupper(
                Str::random(3)
            );
            $abstractId =
                'NZUSI-' .
                date('Y') . '-' .
                $firstLetter .
                $presentationLetter .
                $topicLetter .
                $titleLetter .
                $randomString;
            $submission = AbstractSubmission::create([
                'abstract_id' =>$abstractId,
                'post_user' => Auth::id(),
                'first_name' => trim($request->first_name),
                'last_name' => trim($request->last_name),
                'email' => trim($request->email),
                'phone' => trim($request->phone),
                'institution' => trim($request->institution),
                'designation' => trim($request->designation),
                'city' => trim($request->city),
                'presentation_type' => trim($request->presentation_type),
                'topic_category' => trim($request->topic_category),
                'abstract_title' => trim($request->abstract_title),
                'authors' => trim($request->authors),
                'corresponding_author' => trim($request->corresponding_author),
                'abstract_body' => trim($request->abstract_body),
                'supporting_file' => $fileName,
                'submitted_at' => now(),
                'status' => 'pending',
                'nzusi_membership_no' => trim($request->nzusi_membership_no),
                'usi_membership_no' => trim($request->usi_membership_no),
                'conf_reg_no' => trim($request->conf_reg_no),
                'video_link' => trim($request->video_link),
            ]);
            try {
                $recipients = [
                    'shubhankarchandra@gmail.com',
                    'drsameertrivedi@gmail.com',
                    'drkamaljeet@gmail.com',
                    'nzusioffice@gmail.com',
                    'akshat@gdsons.co.in'
                ];
                if (!empty($request->email)) {
                    Mail::to(trim($request->email))->queue(
                        new AbstractSubmissionMailUser($submission)
                    );
                }
                foreach ($recipients as $email) {
                    Mail::to($email)->queue(
                        new AbstractSubmissionMail(
                            $submission,
                        )
                    );
                }
            } catch (\Exception $mailException) {
                Log::error(
                    'Abstract Submission Mail Error: ' .
                    $mailException->getMessage()
                );
            }
            DB::commit();
            return response()->json([
                'status' => true,
                'message' => 'Abstract submitted successfully.',
                'data' => $submission,
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error(
                'Abstract Submission Error: ' .
                $e->getMessage()
            );
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong. Please try again later.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
