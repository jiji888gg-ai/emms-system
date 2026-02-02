<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AttendanceController;
use App\Http\Controllers\Api\AuthController;


Route::post('/attendance/scan', [AttendanceController::class, 'scan']);

Route::post('/login', [AuthController::class, 'login']);

Route::get('/attendance/history/{s_id}', [AttendanceController::class, 'history']);