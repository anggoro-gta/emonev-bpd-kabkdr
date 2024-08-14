<?php

use Illuminate\Support\Facades\Route;
use Modules\Laporan\App\Http\Controllers\LaporanController;
use Modules\Laporan\App\Http\Controllers\MonitoringOpdController;

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

Route::group(['middleware' => ['auth'], 'prefix' => 'laporan', 'as' => 'laporan.'], function () {
    Route::resource('/monitoring_opd', MonitoringOpdController::class)->only('index');
});