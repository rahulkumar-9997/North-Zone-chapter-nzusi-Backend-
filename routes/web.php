<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Backend\LoginController;
use App\Http\Controllers\Backend\ForgotPasswordController;
use App\Http\Controllers\Backend\DashboardController;
use App\Http\Controllers\Backend\CkeditorController;
use App\Http\Controllers\Backend\CacheController;
use App\Http\Controllers\Backend\PageController;
use App\Http\Controllers\Backend\MenuController;
use App\Http\Controllers\Backend\BlogCategoryController;
use App\Http\Controllers\Backend\BlogPostController;
use App\Http\Controllers\Backend\ManageMemberController;
use App\Http\Controllers\Backend\MemberTypeController;
use App\Http\Controllers\Backend\LabelController;

Route::prefix('admin')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm']);
    Route::post('/login', [LoginController::class, 'login'])->name('login');
    Route::get('forget/password', [ForgotPasswordController::class, 'showForgetPasswordForm'])->name('forget.password');
    Route::post('forget.password', [ForgotPasswordController::class, 'submitForgetPasswordForm'])->name('forget.password.submit');

    Route::get('reset-password/{token}', [ForgotPasswordController::class, 'showResetPasswordForm'])->name('reset.password.get');
    Route::post('reset-password', [ForgotPasswordController::class, 'submitResetPasswordForm'])->name('reset.password.post');
    Route::get('/logout', [LoginController::class, 'logout'])->name('logout');
});
Route::middleware(['auth:web', 'admin'])->group(function () {
    Route::post('ckeditor/upload', [CkeditorController::class, 'upload'])->name('ckeditor.upload');
    Route::get('ckeditor/images', [CkeditorController::class, 'imageList'])->name('ckeditor.images');
    Route::delete('ckeditor/image', [CkeditorController::class, 'deleteImage'])->name('ckeditor.delete');
    
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('member-analytics', [DashboardController::class, 'memberAnalytics'])->name('member.analytics');
    Route::get('/get-daily-visitors', [DashboardController::class, 'getDailyVisitors'])->name('get-daily-visitors');
    Route::get('/clear-cache', [CacheController::class, 'clearCache'])->name('clear-cache');
    Route::resource('pages', PageController::class);
    Route::resource('menus', MenuController::class);
    Route::get('menu/items/{menu}', [MenuController::class, 'displayMenuItem'])->name('menus.items');
    Route::post('menu/{menu}/item', [MenuController::class, 'storeItem'])->name('menu.item.store');
    
    Route::get('menu/{menu}/item/{item}/edit', [MenuController::class, 'editItem'])
    ->name('menu.item.edit');
    Route::put('menu/{menu}/item/{item}', [MenuController::class, 'updateItem'])->name('menu.item.update');
    Route::delete('menu/{menu}/item/{item}', [MenuController::class, 'destroyItem'])->name('menu.item.destroy');
    Route::post('menus/{menu}/items/order', [MenuController::class, 'orderItems'])->name('menus.items.order');

    Route::resource('blog-category', BlogCategoryController::class);
    Route::resource('blog-post', BlogPostController::class);
    Route::delete('/blog-more-image/{id}', [BlogPostController::class, 'deleteImage'])
    ->name('blog.image.delete');

    Route::prefix('manage-member')->name('manage-member.')->group(function () {
        Route::get('import', [ManageMemberController::class, 'importIndex'])->name('import');
        Route::post('import', [ManageMemberController::class, 'importStore'])->name('import.store');
    });

    Route::resource('member-type', MemberTypeController::class);
    Route::resource('manage-member', ManageMemberController::class);
    Route::prefix('manage-member')->group(function () {
        Route::get('step1/{id}', [ManageMemberController::class, 'memberPersonalInfoForm'])->name('manage-member.step1');

        Route::post('store-step1', [ManageMemberController::class, 'storeStep1'])->name('manage-member.store-step1');

        Route::get('step2/{id}', [ManageMemberController::class, 'presentAppointmentDesignationForm'])->name('manage-member.step2');
        Route::post('store-step2/{id}', [ManageMemberController::class, 'storeStep2'])->name('manage-member.store-step2');

        Route::get('step3/{id}', [ManageMemberController::class, 'academicQualificationForm'])->name('manage-member.step3');
        Route::post('store-step3/{id}', [ManageMemberController::class, 'storeStep3'])->name('manage-member.store-step3');

        Route::get('step4/{id}', [ManageMemberController::class, 'trainingInUrologyForm'])->name('manage-member.step4');
        Route::post('store-step4/{id}', [ManageMemberController::class, 'storeStep4'])->name('manage-member.store-step4');
        
        /**Member edit route */
        Route::get('edit/{id}', [ManageMemberController::class, 'edit'])->name('manage-member.edit');
        Route::put('update-step1/{id}', [ManageMemberController::class, 'updateStep1'])->name('manage-member.update-step1');
        Route::put('update-step2/{id}', [ManageMemberController::class, 'updateStep2'])->name('manage-member.update-step2');
        Route::put('update-step3/{id}', [ManageMemberController::class, 'updateStep3'])->name('manage-member.update-step3');
        Route::put('update-step4/{id}', [ManageMemberController::class, 'updateStep4'])->name('manage-member.update-step4');
        /**Member edit route */
    });
    Route::resource('label', LabelController::class);   
    
});