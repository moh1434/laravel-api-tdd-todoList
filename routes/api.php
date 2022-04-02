<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\LabelController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\TodoListController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;

Route::middleware('auth:sanctum')->group(function () {

    Route::apiResource('todo-list', TodoListController::class)->parameters(['todo-list' => 'list']);

    Route::apiResource('todo-list.task', TaskController::class)->parameters(['todo-list' => 'list'])
        ->except('show')->shallow();

    Route::apiResource('label', LabelController::class);

    Route::get('/service/connect/{service}', [ServiceController::class, 'connect'])->name('service.connect');
});


Route::post('task/completed', []);


Route::post('register', RegisterController::class)->name('user.register');

Route::post('login', LoginController::class)->name('user.login');
