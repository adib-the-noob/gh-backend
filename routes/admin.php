<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/dashboard', function (Request $request) {
    return response()->json([
        'message' => 'Welcome to admin dashboard',
        'user' => $request->user()
    ]);
});
