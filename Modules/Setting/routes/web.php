<?php

use Illuminate\Support\Facades\Route;
use Modules\Setting\Http\Controllers\PermissionController;
use Modules\Setting\Http\Controllers\RoleController;
use Modules\Setting\Http\Controllers\UserController;

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

Route::group(['middleware' => ['auth'], 'prefix' => 'setting', 'as' => 'setting.'], function () {
    Route::resource('/user', UserController::class);
    Route::post('/user-datatable', [UserController::class,'datatable'])->name('user.datatable');
    Route::get('/user-assign-role/{id}', [UserController::class,'assignRole'])->name('user.assign-role');
    Route::put('/user-assign-role/{id}', [UserController::class,'updateRole'])->name('user.update-role');
    Route::resource('/permission', PermissionController::class);
    Route::resource('/role', RoleController::class);
});