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

Route::group(['prefix' => 'depositManage', 'as' => 'depositmanage.'], function() {
    Route::post('/deposit', [DepositManageAPIController::class, 'deposit'])->name('deposit');
    Route::post('/callback', [DepositManageAPIController::class, 'callbackDeposit'])->name('callbackDeposit');
});

Route::group(['prefix' => 'withdrawManage', 'as' => 'withdrawmanage.'], function() {
    Route::post('/withdraw', [WithdrawManageAPIController::class, 'withdrawOrder'])->name('withdrawOrder');
    Route::post('/callback', [WithdrawManageAPIController::class, 'callbackWithdraw'])->name('callbackWithdraw');
});

Route::group(['prefix' => 'usermanage', 'as' => 'usermanage.', 'middleware' => 'check.apikey'], function() {
    Route::post('/register', [UserManageAPIController::class, 'register'])->name('register');
    Route::post('/login', [UserManageAPIController::class, 'login'])->name('login');
    Route::post('/customer-detail', [UserManageAPIController::class, 'showCustomerDetail'])->name('showCustomerDetail');
    Route::post('/check-user', [UserManageAPIController::class, 'checkUserID'])->name('checkUserID');
    Route::get('/campaign', [UserManageAPIController::class, 'getAllCampaign'])->name('getAllCampaign');
    Route::post('/calculate-point', [UserManageAPIController::class, 'calculatePoint'])->name('calculatePoint');
});





