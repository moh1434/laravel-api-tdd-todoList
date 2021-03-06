<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\LabelController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\TodoListController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\EmailVerificationController;


Route::middleware('auth:sanctum')->group(function () {

    Route::middleware('notVerified')->group(function () {
        Route::get('/email/verify-before-continue', [EmailVerificationController::class, 'show'])
            ->name('verification.notice');

        Route::post('/email/send', [EmailVerificationController::class, 'send'])
            ->middleware(['throttle:6,1'])
            ->name('verification.send');

        Route::get('/email/verify', [EmailVerificationController::class, 'verify'])
            ->name('verification.verify');
    });

    Route::middleware('verified')->group(function () {
        Route::apiResource('todo-list', TodoListController::class)->parameters(['todo-list' => 'list']);

        Route::apiResource('todo-list.task', TaskController::class)->parameters(['todo-list' => 'list'])
            ->except('show')->shallow();

        Route::apiResource('label', LabelController::class);

        Route::get('/service/connect/{service}', [ServiceController::class, 'connect'])->name('service.connect');
        Route::post('/service/callback', [ServiceController::class, 'callback'])->name('service.callback');

        Route::post('/service/{service}', [ServiceController::class, 'store'])
            ->name('service.store');
    });
});


Route::post('task/completed', []);


Route::post('register', RegisterController::class)->name('user.register');

Route::post('login', LoginController::class)->name('user.login');
