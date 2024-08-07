<?php

use Illuminate\Support\Facades\Route;
use Modules\Realisasi\App\Http\Controllers\SubKegiatanController;
use Modules\Realisasi\App\Http\Controllers\RealisasiController;

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

Route::group([], function () {
    Route::group(['middleware' => ['auth'], 'prefix' => 'realisasi', 'as' => 'realisasi.'], function () {
        // Route::resource('/program', UrusanController::class);
        // Route::resource('/kegiatan', UrusanController::class);
        Route::resource('/sub_kegiatan', SubKegiatanController::class);
        Route::get('/get_sub_kegiatan', [SubKegiatanController::class, 'getSubKegiatan']);
        Route::post('/sub_kegiatan/{id}/toogle_posting', [SubKegiatanController::class, 'tooglePosting'])->name('sub_kegiatan.tooglePosting');
    });
});
