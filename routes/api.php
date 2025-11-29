<?php

use App\Http\Controllers\Api\Admin\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::group(['prefix'=> '/auth/admin'], function () {
    Route::post('/signin', [AuthController::class,'signin']);
    Route::post('/verify-otp', [AuthController::class,'verifyOtp']);
});

Route::group(['prefix'=> '/admin', 'middleware' => ['auth:sanctum', 'admin']], function () {
    Route::get('me', [AuthController::class, 'me']);
});