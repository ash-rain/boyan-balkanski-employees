<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EmployeeCollaborationController;

Route::get('/', [EmployeeCollaborationController::class, 'index'])
    ->name('dashboard');
Route::post('/employee-collaboration/process', [EmployeeCollaborationController::class, 'process'])
    ->name('process');
