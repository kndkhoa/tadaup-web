<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Home\HomeCampainFXAPIController;
use App\Http\Controllers\API\DepositManageAPIController;
use App\Http\Controllers\API\WithdrawManageAPIController;
use App\Http\Controllers\API\UserManageAPIController;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::group(['prefix' => 'homecampainfx', 'as' => 'homecampainfx.'], function() {
    Route::get('/', [HomeCampainFXAPIController::class, 'index'])->name('index');
    Route::get('campainRun', [HomeCampainFXAPIController::class, 'run'])->name('campainRun');
    Route::get('campainDone', [HomeCampainFXAPIController::class, 'done'])->name('campainDone');
    Route::get('campainDetail/{id}', [HomeCampainFXAPIController::class, 'detail'])->name('campainDetail');
    Route::get('contact', [HomeCampainFXAPIController::class, 'contact'])->name('contact');
    Route::get('campainNew', [HomeCampainFXAPIController::class, 'new'])->name('campainNew');
});

// Group routes under depositManage with check.apikey middleware
Route::group(['prefix' => 'depositManage', 'as' => 'depositmanage.', 'middleware' => 'check.apikey'], function() {
    Route::post('/deposit', [DepositManageAPIController::class, 'deposit'])->name('deposit');
    Route::post('/swap', [DepositManageAPIController::class, 'swap'])->name('swap');
});

// Separate route for /callback with check.apikeyopen middleware
Route::post('/depositManage/callback', [DepositManageAPIController::class, 'callbackDeposit'])
    ->name('depositmanage.callbackDeposit')
    ->middleware('check.apikeyopen');  // Use different middleware here

// Separate route for /callback with check.apikeyopen middleware
Route::post('/depositManage/callback-liquid', [DepositManageAPIController::class, 'callbackDepositLiquid'])
    ->name('depositmanage.callbackDepositLiquid')
    ->middleware('check.apikeyopen');  // Use different middleware here

Route::group(['prefix' => 'withdrawManage', 'as' => 'withdrawmanage.'], function() {
    Route::post('/withdraw', [WithdrawManageAPIController::class, 'withdrawOrder'])->name('withdrawOrder');
    Route::post('/approve', [WithdrawManageAPIController::class, 'approve'])->name('approve');
    //Route::post('/callback', [WithdrawManageAPIController::class, 'callbackWithdraw'])->name('callbackWithdraw');
});

Route::post('/withdrawManage/callback', [WithdrawManageAPIController::class, 'callbackWithdraw'])
    ->name('withdrawmanage.callbackWithdraw')
    ->middleware('check.apikeyopen');  // Use different middleware here

Route::group(['prefix' => 'usermanage', 'as' => 'usermanage.', 'middleware' => 'check.apikey'], function() {
    Route::post('/register', [UserManageAPIController::class, 'register'])->name('register');
    Route::post('/login', [UserManageAPIController::class, 'login'])->name('login');
    Route::post('/customer-detail', [UserManageAPIController::class, 'showCustomerDetail'])->name('showCustomerDetail');
    Route::post('/check-user', [UserManageAPIController::class, 'checkUserID'])->name('checkUserID');
    Route::get('/campaign', [UserManageAPIController::class, 'getAllCampaign'])->name('getAllCampaign');
    Route::post('/calculate-point', [UserManageAPIController::class, 'calculatePoint'])->name('calculatePoint');
    Route::post('/update-profile', [UserManageAPIController::class, 'updateProfile'])->name('updateProfile');
    Route::post('/interest-auto', [UserManageAPIController::class, 'interestAuto'])->name('interestAuto');
    Route::get('/list-campaign-protrader', [UserManageAPIController::class, 'getListCampaignProTrader'])->name('getListCampaignProTrader');
    
});





