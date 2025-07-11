<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

use App\Http\Controllers\EmployeeCollaborationController;

require __DIR__ . '/settings.php';
require __DIR__ . '/auth.php';

Route::post('/employee-collaboration/process', [EmployeeCollaborationController::class, 'process']);

Route::get('/', function () {
    return Inertia::render('EmployeeCollaboration');
})->name('home');
