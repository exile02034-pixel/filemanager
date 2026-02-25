<?php

use App\Http\Controllers\User\FileController as UserFileController;
use App\Http\Controllers\User\FolderController as UserFolderController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::middleware(['auth', 'verified', 'role:user'])
    ->prefix('user')
    ->name('user.')
    ->group(function () {
        Route::get('/dashboard', fn () => Inertia::render('user/Dashboard'))->name('dashboard');
        //folders routes
        Route::get('/folders', [UserFolderController::class, 'index'])->name('folders.index');
        Route::get('/folders/{folder}', [UserFolderController::class, 'show'])->name('folders.show');
        Route::post('/folders',[UserFolderController::class, 'store'])->name('folders.store');
        
        //file routes
        Route::get('/files',[UserFileController::class, 'index'])->name('files.index');
    });