<?php

use App\Http\Controllers\User\FileController as UserFileController;
use App\Http\Controllers\User\FolderController as UserFolderController;
use App\Http\Controllers\User\ProfileController as UserProfileController;
use App\Http\Controllers\User\HomeController as UserHomeController;
use App\Http\Controllers\User\MyDriveController as UserMyDriveController;


use Illuminate\Support\Facades\Route;
use Inertia\Inertia;


Route::middleware(['auth', 'verified', 'role:user'])
    ->prefix('user')
    ->name('user.')
    ->group(function () {
        Route::get('/dashboard', fn () => Inertia::render('user/Dashboard'))->name('dashboard');

        // profile route
        Route::get('/profile', [UserProfileController::class, 'index'])->name('profile.index');

        // folders routes
        Route::get('/folders', [UserFolderController::class, 'index'])->name('folders.index');
        Route::get('/folders/{folder}', [UserFolderController::class, 'show'])->name('folders.show');
        Route::post('/folders', [UserFolderController::class, 'store'])->name('folders.store');

        // files routes
        Route::get('/files', [UserFileController::class, 'index'])->name('files.index');

        // Home Routes
        Route::get('/home', [UserHomeController::class, 'index'])->name('home.index');

        //MyDrive Routes
        Route::get('/mydrive', [UserMyDriveController::class, 'index'])->name('mydrive.index');


    });
