<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Backend\LoginController;
use App\Http\Controllers\Backend\ForgotPasswordController;
use App\Http\Controllers\Backend\DashboardController;
use App\Http\Controllers\Backend\CkeditorController;
use App\Http\Controllers\Backend\CacheController;
use App\Http\Controllers\Backend\BlogCategoryController;
use App\Http\Controllers\Backend\BlogSubcategoryController;
use App\Http\Controllers\Backend\BlogPostController;
use App\Http\Controllers\Backend\ManageMemberController;
use App\Http\Controllers\Backend\MemberTypeController;
use App\Http\Controllers\Backend\LabelController;
use App\Http\Controllers\Backend\AbstractSubmissionController;
use App\Http\Controllers\Backend\MenuController;

use App\Http\Controllers\Backend\UserController;
use App\Http\Controllers\Backend\RoleController;
Route::get('/test-analytics', function () {
    $data = \Spatie\Analytics\Facades\Analytics::get(
        \Spatie\Analytics\Period::days(7),
        ['sessions', 'screenPageViews', 'activeUsers']
    );
    return response()->json($data);
});
/*
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Google\Client;
Route::get('/test-analytics', function () {
    $data = \Spatie\Analytics\Facades\Analytics::get(
        \Spatie\Analytics\Period::days(7),
        ['sessions', 'screenPageViews', 'activeUsers']
    );
    return response()->json($data);
});
Route::get('/direct-test', function () {

    putenv('GOOGLE_APPLICATION_CREDENTIALS=' . storage_path('app/analytics/service-account-credentials.json'));

    $client = new \Google\Client();
    $client->useApplicationDefaultCredentials();
    $client->setScopes(['https://www.googleapis.com/auth/analytics.readonly']);

    $http = $client->authorize();

    try {
        $res = $http->post(
            'https://analyticsdata.googleapis.com/v1beta/properties/539589973:runReport',
            ['json' => [
                'dateRanges' => [['startDate' => '7daysAgo', 'endDate' => 'today']],
                'metrics'    => [['name' => 'activeUsers']],
            ]]
        );
        return response()->json(json_decode($res->getBody(), true));
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()]);
    }
});
Route::get('/fix-ga4', function () {

    putenv('GOOGLE_APPLICATION_CREDENTIALS=' . storage_path('app/analytics/service-account-credentials.json'));

    $client = new \Google\Client();
    $client->useApplicationDefaultCredentials();
    $client->setScopes([
        'https://www.googleapis.com/auth/analytics.manage.users',
        'https://www.googleapis.com/auth/analytics.readonly',
        'https://www.googleapis.com/auth/analytics.edit',
    ]);

    $http = $client->authorize();

    // Step 1 - Account summaries dekho
    try {
        $res = $http->get('https://analyticsadmin.googleapis.com/v1beta/accountSummaries');
        $summaries = json_decode($res->getBody(), true);
    } catch (\Exception $e) {
        $summaries = ['error' => $e->getMessage()];
    }

    // Step 2 - Properties list karo
    try {
        $res2 = $http->get('https://analyticsadmin.googleapis.com/v1beta/properties?filter=ancestor:accounts/52770125');
        $properties = json_decode($res2->getBody(), true);
    } catch (\Exception $e) {
        $properties = ['error' => $e->getMessage()];
    }

    // Step 3 - Direct grant try karo
    try {
        $res3 = $http->post(
            'https://analyticsadmin.googleapis.com/v1beta/properties/539589973/accessBindings',
            ['json' => [
                'user'  => 'nzusi-932@nzusi-498210.iam.gserviceaccount.com',
                'roles' => ['predefinedRoles/viewer'],
            ]]
        );
        $grant = json_decode($res3->getBody(), true);
    } catch (\Exception $e) {
        $grant = ['error' => $e->getMessage()];
    }

    return response()->json([
        'summaries'  => $summaries,
        'properties' => $properties,
        'grant'      => $grant,
    ]);
});
Route::get('/find-property', function () {

    $jsonPath = storage_path('app/analytics/service-account-credentials.json');
    $serviceAccount = json_decode(file_get_contents($jsonPath), true);

    $now = time();
    $header  = rtrim(strtr(base64_encode(json_encode(['alg' => 'RS256', 'typ' => 'JWT'])), '+/', '-_'), '=');
    $payload = rtrim(strtr(base64_encode(json_encode([
        'iss'   => $serviceAccount['client_email'],
        'scope' => 'https://www.googleapis.com/auth/analytics.manage.users',
        'aud'   => 'https://oauth2.googleapis.com/token',
        'exp'   => $now + 3600,
        'iat'   => $now,
    ])), '+/', '-_'), '=');

    $unsignedJwt = $header . '.' . $payload;
    openssl_sign($unsignedJwt, $signature, $serviceAccount['private_key'], 'SHA256');
    $signedJwt = $unsignedJwt . '.' . rtrim(strtr(base64_encode($signature), '+/', '-_'), '=');

    $tokenRes = Http::asForm()->post('https://oauth2.googleapis.com/token', [
        'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
        'assertion'  => $signedJwt,
    ]);

    $token = $tokenRes->json('access_token');

    // Account 52770125 ke andar saari properties list karo
    $result = Http::withToken($token)
        ->get('https://analyticsadmin.googleapis.com/v1beta/properties', [
            'filter' => 'ancestor:accounts/52770125',
        ]);

    return response()->json([
        'token'      => 'OK',
        'status'     => $result->status(),
        'properties' => $result->json(),
    ]);
});
Route::get('/check-property', function () {

    $jsonPath = storage_path('app/analytics/service-account-credentials.json');
    $serviceAccount = json_decode(file_get_contents($jsonPath), true);

    $now = time();
    $header  = rtrim(strtr(base64_encode(json_encode(['alg' => 'RS256', 'typ' => 'JWT'])), '+/', '-_'), '=');
    $payload = rtrim(strtr(base64_encode(json_encode([
        'iss'   => $serviceAccount['client_email'],
        'scope' => 'https://www.googleapis.com/auth/analytics.readonly',
        'aud'   => 'https://oauth2.googleapis.com/token',
        'exp'   => $now + 3600,
        'iat'   => $now,
    ])), '+/', '-_'), '=');

    $unsignedJwt = $header . '.' . $payload;
    openssl_sign($unsignedJwt, $signature, $serviceAccount['private_key'], 'SHA256');
    $signedJwt = $unsignedJwt . '.' . rtrim(strtr(base64_encode($signature), '+/', '-_'), '=');

    $tokenResponse = Http::asForm()->post('https://oauth2.googleapis.com/token', [
        'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
        'assertion'  => $signedJwt,
    ]);

    $token = $tokenResponse->json('access_token');

    // List all accounts service account can see
    $accounts = Http::withToken($token)
        ->get('https://analyticsadmin.googleapis.com/v1beta/accountSummaries');

    return response()->json($accounts->json());
});
Route::get('/check-access', function () {

    $jsonPath = storage_path('app/analytics/service-account-credentials.json');
    $serviceAccount = json_decode(file_get_contents($jsonPath), true);

    $now = time();
    $header  = rtrim(strtr(base64_encode(json_encode(['alg' => 'RS256', 'typ' => 'JWT'])), '+/', '-_'), '=');
    $payload = rtrim(strtr(base64_encode(json_encode([
        'iss'   => $serviceAccount['client_email'],
        'scope' => 'https://www.googleapis.com/auth/analytics.manage.users https://www.googleapis.com/auth/analytics.readonly',
        'aud'   => 'https://oauth2.googleapis.com/token',
        'exp'   => $now + 3600,
        'iat'   => $now,
    ])), '+/', '-_'), '=');

    $unsignedJwt = $header . '.' . $payload;
    openssl_sign($unsignedJwt, $signature, $serviceAccount['private_key'], 'SHA256');
    $signedJwt = $unsignedJwt . '.' . rtrim(strtr(base64_encode($signature), '+/', '-_'), '=');

    $tokenRes = Http::asForm()->post('https://oauth2.googleapis.com/token', [
        'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
        'assertion'  => $signedJwt,
    ]);

    $token = $tokenRes->json('access_token');

    // Check access bindings on property
    $result = Http::withToken($token)
        ->get('https://analyticsadmin.googleapis.com/v1beta/properties/539589973/accessBindings');

    return response()->json([
        'token'    => $token ? 'OK' : 'FAILED',
        'status'   => $result->status(),
        'response' => $result->json(),
    ]);
});
*/

Route::prefix('admin')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm']);
    Route::post('/login', [LoginController::class, 'login'])->name('login');
    Route::get('forget/password', [ForgotPasswordController::class, 'showForgetPasswordForm'])->name('forget.password');
    Route::post('forget.password', [ForgotPasswordController::class, 'submitForgetPasswordForm'])->name('forget.password.submit');

    Route::get('reset-password/{token}', [ForgotPasswordController::class, 'showResetPasswordForm'])->name('reset.password.get');
    Route::post('reset-password', [ForgotPasswordController::class, 'submitResetPasswordForm'])->name('reset.password.post');
    Route::get('/logout', [LoginController::class, 'logout'])->name('logout');
});
// Route::middleware(['auth:web', 'admin'])->group(function () {
Route::middleware(['auth:web', 'permission'])->group(function () {
    Route::post('ckeditor/upload', [CkeditorController::class, 'upload'])->name('ckeditor.upload');
    Route::get('ckeditor/images', [CkeditorController::class, 'imageList'])->name('ckeditor.images');
    Route::delete('ckeditor/image', [CkeditorController::class, 'deleteImage'])->name('ckeditor.delete');
    
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('member-analytics', [DashboardController::class, 'memberAnalytics'])->name('member.analytics');
    Route::get('/get-daily-visitors', [DashboardController::class, 'getDailyVisitors'])->name('get-daily-visitors');
    Route::get('/clear-cache', [CacheController::class, 'clearCache'])->name('clear-cache');
    
    Route::resource('blog-category', BlogCategoryController::class);
    Route::resource('blog-subcategory', BlogSubcategoryController::class);
    Route::get('blog-subcategories/{categoryId}', [BlogPostController::class, 'getSubcategories'])
    ->name('blog.subcategories');
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
    Route::get('abstract-submission', [AbstractSubmissionController::class, 'index'])->name('abstract-submission.index');
    Route::get('abstract-submission/{id}', [AbstractSubmissionController::class, 'show'])->name('abstract-submission.show');
    Route::delete('abstract-submission/{id}', [AbstractSubmissionController::class, 'destroy'])->name('abstract-submission.destroy');

    // ========== USER MANAGEMENT ROUTES ==========
    Route::resource('users', UserController::class);
    Route::get('users/{user}/roles', [UserController::class, 'roles'])->name('users.roles');
    Route::put('users/{user}/roles', [UserController::class, 'updateRoles'])->name('users.update-roles');
    
    // ========== ROLE MANAGEMENT ROUTES ==========
    Route::resource('roles', RoleController::class);
    Route::get('roles/{role}/menus', [RoleController::class, 'menus'])->name('roles.menus');
    Route::put('roles/{role}/menus', [RoleController::class, 'updateMenus'])->name('roles.update-menus');
    
    // ========== MENU MANAGEMENT ROUTES ==========
    Route::resource('menus', MenuController::class);
    Route::post('menus/{menu}/order',[MenuController::class, 'updateOrder'])->name('menus.update-order');

    Route::post('menus/{menu}/toggle-status',[MenuController::class, 'toggleStatus'])->name('menus.toggle-status');
    Route::post('menus/{menu}/sidebar-status',[MenuController::class, 'toggleSidebarStatus'])->name('menus.toggle-sidebar-status');
});