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
    Route::post('/member-types/{memberType}/restore', [MemberTypeController::class, 'restore'])->name('member-types.restore');
    Route::resource('member-types', MemberTypeController::class)->except(['show']);
    Route::post('/breeding-numbers/{breedingNumber}/restore', [BreedingNumberController::class, 'restore'])->name('breeding-numbers.restore');
    Route::resource('breeding-numbers', BreedingNumberController::class)->withTrashed(['show']);
});
