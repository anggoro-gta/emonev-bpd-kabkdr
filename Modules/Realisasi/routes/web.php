<?php

use Illuminate\Support\Facades\Route;
use Modules\Realisasi\App\Http\Controllers\KegiatanController;
use Modules\Realisasi\App\Http\Controllers\ProgramController;
use Modules\Realisasi\App\Http\Controllers\SubKegiatanController;

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

        Route::resource('/program', ProgramController::class);
        Route::get('/get_program', [ProgramController::class, 'getKegiatan']);
        Route::post('/program/{id}/toogle_posting', [ProgramController::class, 'tooglePosting'])->name('program.tooglePosting');

        Route::resource('/kegiatan', KegiatanController::class);
        Route::get('/get_kegiatan', [KegiatanController::class, 'getKegiatan']);
        Route::post('/kegiatan/{id}/toogle_posting', [KegiatanController::class, 'tooglePosting'])->name('kegiatan.tooglePosting');

        Route::resource('/sub_kegiatan', SubKegiatanController::class);
        Route::get('/get_sub_kegiatan', [SubKegiatanController::class, 'getSubKegiatan']);
        Route::post('/sub_kegiatan/{id}/toogle_posting', [SubKegiatanController::class, 'tooglePosting'])->name('sub_kegiatan.tooglePosting');
    });
});
