<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\BlogController;
use App\Http\Controllers\Api\MemberAuthController;
use App\Http\Controllers\Api\MemberController;

Route::get('blog-category', [BlogController::class, 'blogCategory']);
Route::get('blog-category/{slug}', [BlogController::class, 'categoryWiseBlogList']);
Route::get('blog', [BlogController::class, 'blogList']);
Route::get('blog/{slug}', [BlogController::class, 'blogDetails']);

Route::prefix('member')->group(function () {
    /* Public APIs */
    Route::controller(MemberAuthController::class)->group(function () {        
        Route::post('/login', 'loginOrCreateAccountWithOtp');
        Route::post('/send-otp', 'sendOtp')->middleware('throttle:5,1');
        Route::post('/verify-otp', 'verifyOtpAndLogin')->middleware('throttle:10,1');
        Route::post('/resend-otp', 'resendOtp');
        Route::post('/check-contact', 'checkContactExists');
        Route::post('/google-login', 'googleLogin');
    });
    /* Protected APIs */
    Route::middleware(['auth:sanctum'])->group(function () {
        Route::controller(MemberController::class)->group(function () {
            Route::get('/profile', 'profile');
            Route::post('/update-profile', 'updateProfile');
            Route::get('/profile/address', [MemberController::class, 'getAddress']);
            Route::put('/profile/address', [MemberController::class, 'updateAddress']);
            Route::get('present-appointment-designation', [MemberController::class, 'getPresentAppointmentDesignation']);
            Route::put('present-appointment-designation', [MemberController::class, 'updatePresentAppointmentDesignation']);

            Route::get('academic-qualification', [MemberController::class, 'getAcademicQualification']);
            Route::put('academic-qualification', [MemberController::class, 'updateAcademicQualification']);

            Route::get('training-in-urology', [MemberController::class, 'getTrainingInUrology']);
            Route::put('training-in-urology', [MemberController::class, 'updateTrainingInUrology']);

            Route::post('logout', 'logout');
        });        
    });
    
});

