<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BreedingNumberController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\MemberTypeController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/dashboard');
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'create'])->name('login');
    Route::post('/login', [AuthController::class, 'store'])->middleware('throttle:10,1');
});
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'destroy'])->name('logout');
    Route::get('/dashboard', DashboardController::class)->name('dashboard');
    Route::post('/members/{member}/restore', [MemberController::class, 'restore'])->name('members.restore');
    Route::resource('members', MemberController::class)->withTrashed(['show']);
    Route::resource('member-types', MemberTypeController::class)->only(['index', 'edit', 'update']);
    Route::get('/breeding-numbers', [BreedingNumberController::class, 'index'])->name('breeding-numbers.index');
});
