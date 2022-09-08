<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DiskominfoController;
use App\Http\Controllers\BappelitbangController;
use App\Http\Controllers\KemenagController;
use App\Http\Controllers\BpomController;
use App\Http\Controllers\DppkbController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('pages.index');
});

Route::prefix('form')->group(function () {
    //diskominfo
    Route::get('/diskominfo', [DiskominfoController::class, 'index']);
    Route::post('/diskominfo/submit', [DiskominfoController::class, 'store']);
    Route::get('/diskominfo/{date}', [DiskominfoController::class, 'edit']);
    Route::post('/diskominfo/update', [DiskominfoController::class, 'update']);
    //bappeliitbang
    Route::get('/bappelitbang', [BappelitbangController::class, 'index']);
    Route::post('/bappelitbang/submit', [BappelitbangController::class, 'store']);
    Route::get('/bappelitbang/{date}', [BappelitbangController::class, 'edit']);
    Route::post('/bappelitbang/update', [BappelitbangController::class, 'update']);
    //kemenag
    Route::get('/kemenag', [KemenagController::class, 'index']);
    Route::post('/kemenag/submit', [KemenagController::class, 'store']);
    Route::get('/kemenag/{date}', [KemenagController::class, 'edit']);
    Route::post('/kemenag/update', [KemenagController::class, 'update']);
    //bpom
    Route::get('/bpom', [BpomController::class, 'index']);
    Route::post('/bpom/submit', [BpomController::class, 'store']);
    Route::get('/bpom/{date}', [BpomController::class, 'edit']);
    Route::post('/bpom/update', [BpomController::class, 'update']);
    //dppkb
    Route::get('/Dppkb', [DppkbController::class, 'index']);
    Route::post('/Dppkb/submit', [DppkbController::class, 'store']);
    Route::get('/Dppkb/{date}', [DppkbController::class, 'edit']);
    Route::post('/Dppkb/update', [DppkbController::class, 'update']);
});
