<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Home\HomeCampainFXAPIController;


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