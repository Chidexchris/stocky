<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SuperAdminController;
use App\Http\Controllers\SuperAdmin\PlansController;
use App\Http\Controllers\SuperAdmin\SubscriptionsController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/
if (!file_exists(base_path('public/install/installed.lock'))) {
    Route::get('/', function () {
        return redirect('/install');
    });
}

Route::get('/', function () {
    return view('auth.login');
})->middleware('guest');

Auth::routes(['register' => false]);

// User Registration (explicitly enabled)
Route::get('/register', 'Auth\RegisterController@showRegistrationForm')
    ->name('register')
    ->middleware('guest');
Route::post('/register', 'Auth\RegisterController@register')
    ->middleware('guest');

Route::group(['middleware' => 'auth'], function () {
    Route::get('/home', 'HomeController@index')
        ->name('home');

    Route::get('/sales-purchases/chart-data', 'HomeController@salesPurchasesChart')
        ->name('sales-purchases.chart');

    Route::get('/current-month/chart-data', 'HomeController@currentMonthChart')
        ->name('current-month.chart');

    Route::get('/payment-flow/chart-data', 'HomeController@paymentChart')
        ->name('payment-flow.chart');

    // Pricing & Subscription
    Route::get('/pricing', [App\Http\Controllers\SubscriptionController::class, 'pricing'])->name('saas.pricing');
    Route::post('/pricing/{plan}', [App\Http\Controllers\SubscriptionController::class, 'selectPlan'])->name('saas.pricing.select');
});

// Dedicated Super Admin Gateway
Route::get('/super-admin/login', [App\Http\Controllers\Auth\SuperAdminLoginController::class, 'showLoginForm'])->name('superadmin.login');
Route::post('/super-admin/login', [App\Http\Controllers\Auth\SuperAdminLoginController::class, 'login']);
Route::post('/super-admin/logout', [App\Http\Controllers\Auth\SuperAdminLoginController::class, 'logout'])->name('superadmin.logout');

Route::group(['middleware' => ['auth', 'role:Super Admin']], function () {
    Route::get('/admin', 'Admin\DashboardController@index')->name('admin.dashboard');

    // Platform Management (Master Portal)
    Route::group(['prefix' => 'platform', 'as' => 'saas.'], function () {
        Route::get('/dashboard', 'SuperAdminController@dashboard')->name('dashboard');
        Route::get('/businesses', 'SuperAdminController@businesses')->name('businesses.index');
        Route::post('/businesses/{business}/toggle-status', 'SuperAdminController@toggleBusinessStatus')->name('businesses.toggle');
        Route::post('/businesses/{business}/verify', 'SuperAdminController@updateVerificationStatus')->name('businesses.verify');
        Route::post('/businesses/{business}/impersonate', 'SuperAdminController@impersonate')->name('businesses.impersonate');
        Route::post('/businesses/{business}/change-plan', 'SuperAdminController@changePlan')->name('businesses.plan');
        Route::post('/businesses/{business}/cancel-trial', 'SuperAdminController@cancelTrial')->name('businesses.cancel-trial');
        Route::get('/businesses/{business}/features', 'SuperAdminController@editFeatures')->name('businesses.features.edit');
        Route::post('/businesses/{business}/features', 'SuperAdminController@updateFeatures')->name('businesses.features.update');
        Route::delete('/businesses/{business}', 'SuperAdminController@destroy')->name('businesses.destroy');
        Route::get('/users', 'SuperAdminController@users')->name('users.index');
        Route::get('/audit', 'SuperAdminController@audit')->name('audit.index');
        
        Route::get('/broadcast', 'SuperAdmin\BroadcastController@create')->name('broadcast.create');
        Route::post('/broadcast', 'SuperAdmin\BroadcastController@send')->name('broadcast.send');

        Route::get('/transactions', 'SuperAdmin\FinanceController@transactions')->name('finance.transactions');
        Route::post('/transactions/{transaction}/refund', 'SuperAdmin\FinanceController@refund')->name('finance.refund');
        
        Route::get('/coupons', 'SuperAdmin\FinanceController@coupons')->name('coupons.index');
        Route::post('/coupons', 'SuperAdmin\FinanceController@storeCoupon')->name('coupons.store');
        Route::delete('/coupons/{coupon}', 'SuperAdmin\FinanceController@destroyCoupon')->name('coupons.destroy');

        Route::get('/infrastructure', 'SuperAdmin\InfrastructureController@index')->name('infrastructure.index');
        Route::post('/infrastructure/businesses/{business}/maintenance', 'SuperAdmin\InfrastructureController@toggleMaintenance')->name('infrastructure.maintenance');
        Route::post('/infrastructure/scan', 'SuperAdmin\InfrastructureController@scanOrphanedFiles')->name('infrastructure.scan');

        Route::get('/security/sessions', 'SuperAdmin\SecurityController@sessions')->name('security.sessions');
        Route::post('/security/sessions/{id}/terminate', 'SuperAdmin\SecurityController@terminateSession')->name('security.sessions.terminate');

        Route::resource('plans', 'SuperAdmin\PlansController');
        
        Route::get('/subscriptions', 'SuperAdmin\SubscriptionsController@index')->name('subscriptions.index');
        Route::post('/subscriptions/{subscription}/cancel', 'SuperAdmin\SubscriptionsController@cancel')->name('subscriptions.cancel');
        Route::post('/subscriptions/{subscription}/activate', 'SuperAdmin\SubscriptionsController@activate')->name('subscriptions.activate');
        Route::post('/subscriptions/{subscription}/extend-trial', 'SuperAdmin\SubscriptionsController@extendTrial')->name('subscriptions.extend-trial');

        Route::resource('affiliates', 'SuperAdmin\AffiliateController');
        Route::post('/affiliates/{affiliate}/toggle-status', 'SuperAdmin\AffiliateController@toggleStatus')->name('affiliates.toggle-status');
        Route::get('/withdrawals', 'SuperAdmin\WithdrawalController@index')->name('withdrawals.index');
        Route::put('/withdrawals/{withdrawal}', 'SuperAdmin\WithdrawalController@update')->name('withdrawals.update');
    });
    
    // Stop Impersonation
    Route::get('/stop-impersonation', 'SuperAdminController@stopImpersonate')->name('saas.stop-impersonate');
});
