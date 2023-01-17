<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\UserManagementController;
use App\Http\Controllers\VendorController;
use App\Http\Controllers\HomeController;

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

Route::get('/', [LoginController::class, 'index'])->name('login')->middleware('guest');
Route::post('/', [LoginController::class, 'loginProcess']);
Route::post('/logout', [LoginController::class, 'logout']);


Route::group(['middleware' => ['auth','Ceklevel:Supervisor,Admin,Vendor']],function(){
    Route::get('/home', [HomeController::class, 'index']);
    
    Route::resource('/vendor', VendorController::class);
    Route::post('/UploadFile', [VendorController::class, 'UploadFile'])->name('uploadFile');
    Route::put('/AddFile', [VendorController::class, 'create']);
    Route::post('/DeleteFile', [VendorController::class, 'destroy']);
    Route::put('/EditFile', [VendorController::class, 'update']);
    Route::get('/download/{file}', [VendorController::class, 'downloadFile']);
});
Route::group(['middleware' => ['auth','Ceklevel:Supervisor,Admin']],function(){
    Route::resource('/userManagement', UserManagementController::class);
    Route::post('/AddUser', [UserManagementController::class, 'create']);
    Route::put('/EditUser/{id}', [UserManagementController::class, 'update']);
    Route::post('/DeleteUser', [UserManagementController::class, 'destroy']);
});