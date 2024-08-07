<?php

use Illuminate\Support\Facades\Route;
use Modules\Master\App\Http\Controllers\BidangUrusanController;
use Modules\Master\App\Http\Controllers\KegiatanController;
use Modules\Master\App\Http\Controllers\MasterController;
use Modules\Master\App\Http\Controllers\ProgramController;
use Modules\Master\App\Http\Controllers\SkpdController;
use Modules\Master\App\Http\Controllers\SkpdUnitController;
use Modules\Master\App\Http\Controllers\SubKegiatanController;
use Modules\Master\App\Http\Controllers\UrusanController;

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

Route::group(['middleware' => ['auth'], 'prefix' => 'master', 'as' => 'master.'], function () {
    Route::resource('/urusan', UrusanController::class);
    Route::resource('/bidang_urusan', BidangUrusanController::class);
    Route::resource('/skpd', SkpdController::class);
    Route::resource('/skpd_unit', SkpdUnitController::class);
    Route::resource('/program', ProgramController::class);
    Route::resource('/kegiatan', KegiatanController::class);
    Route::resource('/sub_kegiatan', SubKegiatanController::class);
    Route::delete('/program/{id}/indikator', [ProgramController::class, 'destroyIndikator']);
    Route::delete('/kegiatan/{id}/indikator', [KegiatanController::class, 'destroyIndikator']);
});