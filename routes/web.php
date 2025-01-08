<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Controller;
use App\Http\Controllers\Auth\LoginRegisterController;
use App\Http\Controllers\Customer\CustomerRegisterController;
use App\Http\Controllers\Transaction\TransactionController;
use App\Http\Controllers\Campain\CampainFXController;
use App\Http\Controllers\Campain\CampainFXTXNController;
use App\Http\Controllers\Home\HomeCampainFXController;
use App\Http\Controllers\UserManage\UserManageController;
use App\Http\Controllers\DepositManage\DepositManageController;
use App\Http\Controllers\WithdrawManage\WithdrawManageController;




/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/



//Login Controller
Route::controller(LoginRegisterController::class)->group(function() {
    Route::get('/register', 'register')->name('register');
    Route::post('/store', 'store')->name('store');
    Route::get('/login', 'login')->name('login');
    Route::post('/auth', 'auth')->name('auth');
    Route::post('/checklogin', 'checklogin')->name('checklogin');
    Route::get('/dashboard', 'dashboard')->name('dashboard')->middleware('auth');
    Route::post('/logout', 'logout')->name('logout');
});

//Customer Controller
Route::controller(CustomerRegisterController::class)->middleware('auth')->group(function() {
    Route::get('/profile', 'profile')->name('profile');
    Route::get('/edit', 'edit')->name('edit');
    Route::put('/update', 'update')->name('update');
    Route::get('/change-password', 'showChangePasswordForm')->name('showChangePasswordForm');
    Route::post('/change-password', 'changePassword')->name('changePassword');
});

//Transaction Controller
Route::controller(TransactionController::class)->middleware('auth')->group(function() {
    Route::get('/transaction-history', 'transactionHistory')->name('transaction-history');
    Route::get('/withdraw-commission', 'withdrawCommission')->name('withdraw-commission');
    Route::put('/withdraw', 'withdraw')->name('withdraw');
    Route::get('/calculate-commission', 'calculateCommission')->name('calculate-commission')->middleware('level:0');
    Route::put('/calculate', 'calculate')->name('calculate')->middleware('level:0')->middleware('level:0');;
    Route::put('/calculateCustom', 'calculateCustom')->name('calculateCustom')->middleware('level:0')->middleware('level:0');;
    Route::get('/approve-commission', 'approveCommission')->name('approve-commission')->middleware('level:0');
    Route::post('/transaction/{id}/approve', 'approve')->name('transaction.approve')->middleware('level:0');;
    Route::post('/transaction/{id}/reject', 'reject')->name('transaction.reject')->middleware('level:0');;
});

//Campain Controller
Route::controller(CampainFXController::class)->middleware('auth')->group(function() {
    Route::get('/campain-editor', 'campainEditor')->name('campain-editor')->middleware('level:0');
    Route::post('/campain/save', 'save')->name('campain.save')->middleware('level:0');
    Route::get('/campain-new', 'campainNew')->name('campain-new');
    Route::get('/campain-history', 'campainHistory')->name('campain-history');
    Route::post('/campain/{id}/delete', 'delete')->name('campainFX.delete')->middleware('level:0');;
    Route::post('/campain/{id}/edit', 'edit')->name('campainFX.edit')->middleware('level:0');
    Route::match(['get', 'post'],'/campain/{id}/detail', 'detail')->name('campainFX.detail');
    //Route::match(['get', 'post'], 'campain/{id}/detail', [CampaignController::class, 'detail'])->name('campainFX.detail');
    Route::post('/campain/{id}/run', 'run')->name('campainFX.run');
    Route::post('/campain/{id}/done', 'done')->name('campainFX.done');
    Route::post('/campain/deposit', 'deposit')->name('campainFX.deposit');
    Route::get('/campain-transaction-list', 'transactionList')->name('campain-transaction-list')->middleware('level:0');
});

//Campain Controller
Route::controller(CampainFXTXNController::class)->middleware('auth')->group(function() {
    Route::match(['get', 'post'],'/campain-transaction/{id}/detail', 'transactionDetail')->name('campainFXTXN.campain-transaction-detail')->middleware('level:0');  
    Route::post('/campain-transaction/{id}/approve', 'approve')->name('campainFXTXN.approve')->middleware('level:0');  
    Route::post('/campain-transaction/{id}/reject', 'reject')->name('campainFXTXN.reject')->middleware('level:0');  
    Route::match(['get', 'post'],'/campain-transaction/{id}/submit-payment', 'submitPayment')->name('campainFXTXN.submit-payment')->middleware('level:0');  
});

Route::controller(HomeCampainFXController::class)->group(function() {
    Route::get('/', 'new')->name('campainNew');
    Route::get('/campainRun', 'run')->name('campainRun');
    Route::get('/campainDone', 'done')->name('campainDone');
    Route::get('/campainDetail/{id}', 'detail')->name('campainDetail');
    Route::get('/contact', 'contact')->name('contact');
});


//User Management Controller
Route::controller(UserManageController::class)->middleware('auth')->group(function() {
    Route::get('/cutomer-list', 'showCustomerList')->name('showCustomerList')->middleware('level:0');
    Route::get('/wallettada-list', 'showWalletTada')->name('showWalletTada')->middleware('level:0');
    Route::get('/wallettada-history', 'showWalletTadaHistory')->name('showWalletTadaHistory')->middleware('level:0');
    Route::post('/calculate-point', 'calculatePoint')->name('calculatePoint')->middleware('level:0');
    Route::post('/deposit-income', 'depositWalletTadaIncome')->name('depositWalletTadaIncome')->middleware('level:0');
    Route::match(['get', 'post'],'/customer-detail/{id}', 'showCustomerDetail')->name('showCustomerDetail')->middleware('level:0');
    Route::match(['get', 'post'],'/activeProTrader/{id}', 'activeProTrader')->name('activeProTrader')->middleware('level:0');
    Route::post('/creatConnection', 'creatConnection')->name('creatConnection')->middleware('level:0');
    Route::post('/deleteConnection/{id}/delete', 'deleteConnection')->name('deleteConnection')->middleware('level:0');
    Route::get('/commissionmlm', 'showCommissionMLM')->name('showCommissionMLM')->middleware('level:0');
    Route::post('/calculate-mlm/{id}/calculate', 'calculateMLM')->name('calculateMLM')->middleware('level:0');
    Route::get('/report-trading/{id?}', 'showReportTrading')->name('showReportTrading')->middleware('level:0');
    Route::get('/user-activity', 'showUserActiveCore')->name('showUserActiveCore')->middleware('level:0');
});

//Deposit Management Controller
Route::controller(DepositManageController::class)->middleware('auth')->group(function() {
    Route::get('/campaignTransaction', 'showCampaignList')->name('showCampaignList')->middleware('level:0');
    Route::match(['get', 'post'], '/campaignTransaction/{id}/deposit-detail', 'depositDetail')->name('depositDetail')->middleware('level:0');
    Route::match(['get', 'post'], '/campaignTransaction/{id}/register-fund', 'registerFund')->name('registerFund')->middleware('level:0');
    Route::post('/registerFundByID', 'registerFundByID')->name('registerFundByID')->middleware('level:0');
    Route::get('/depositWait', 'showDepositWait')->name('showDepositWait')->middleware('level:0');
    Route::get('/depositDone', 'showDepositDone')->name('showDepositDone')->middleware('level:0');
    Route::get('/depositProcess', 'showDepositProcess')->name('showDepositProcess')->middleware('level:0');
    Route::get('/depositWin', 'showDepositWin')->name('showDepositWin')->middleware('level:0');
    Route::get('/depositReject', 'showDepositReject')->name('showDepositReject')->middleware('level:0');
    Route::post('/campaignTransaction/{id}/approve-wallet', 'approveWallet')->name('approveWallet')->middleware('level:0');  
    Route::post('/campaignTransaction/{id}/approve', 'depositApprove')->name('depositApprove')->middleware('level:0');  
    Route::post('/campaignTransaction/{id}/reject', 'depositReject')->name('depositReject')->middleware('level:0');  
    Route::post('/campaignTransaction/{id}/process', 'depositProcess')->name('depositProcess')->middleware('level:0');  
    Route::post('/campaignTransaction/{id}/win', 'depositWin')->name('depositWin')->middleware('level:0');  
    Route::post('/campaignTransaction/{id}/depositIncome', 'depositIncome')->name('depositIncome')->middleware('level:0');  
});

//Withdraw Management Controller
//test cai nha
Route::controller(WithdrawManageController::class)->middleware('auth')->group(function() {
    Route::get('/withdrawTransaction', 'showWithDrawList')->name('showWithDrawList')->middleware('level:0');
    Route::get('/withdrawHistory', 'showWithDrawHistory')->name('showWithDrawHistory')->middleware('level:0');
    Route::get('/withdrawForm', 'showWithDrawForm')->name('showWithDrawForm')->middleware('level:0');
    Route::post('/withdraw/{id}/approve', 'approve')->name('withdraw.approve')->middleware('level:0');;
    Route::post('/withdraw/{id}/reject', 'reject')->name('withdraw.reject')->middleware('level:0');;
});
