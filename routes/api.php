<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CheckoutAuthController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

/*
|--------------------------------------------------------------------------
| Checkout Auth (public — no auth middleware)
|--------------------------------------------------------------------------
*/
Route::prefix('checkout')->group(function () {
    Route::get('/plans', [CheckoutAuthController::class, 'getPlans']);
    Route::post('/check-email', [CheckoutAuthController::class, 'checkEmail']);
    Route::post('/login', [CheckoutAuthController::class, 'login']);
    Route::post('/register', [CheckoutAuthController::class, 'register']);
    Route::post('/validate-coupon', [CheckoutAuthController::class, 'validateCoupon']);
    Route::post('/subscribe', [CheckoutAuthController::class, 'subscribe']);
    Route::post('/webhook/opay', [CheckoutAuthController::class, 'handleWebhook'])->name('checkout.webhook.opay');
});

// AI Customer Agent
Route::post('/ai/chat', [\App\Http\Controllers\Api\AIController::class, 'chat']);

// Affiliate Routes
Route::post('/checkout/affiliate/register', [App\Http\Controllers\Api\AffiliateController::class, 'register']);
Route::post('/checkout/affiliate/stats', [App\Http\Controllers\Api\AffiliateController::class, 'getStats']);
Route::post('/checkout/affiliate/withdraw', [App\Http\Controllers\Api\AffiliateController::class, 'withdraw']);

// Support Routes
Route::post('/support/ticket', [\App\Http\Controllers\Api\SupportController::class, 'submitTicket']);

// Offline Sync
Route::group(['middleware' => 'auth'], function () {
    Route::get('/pos/products-for-cache', [\Modules\Sale\Http\Controllers\Api\SyncController::class, 'getProductsForCache']);
    Route::post('/sync/offline-data', [\Modules\Sale\Http\Controllers\Api\SyncController::class, 'syncOfflineData']);
});
