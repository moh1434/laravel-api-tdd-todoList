<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\TodoListController;

Route::apiResource('todo-list', TodoListController::class)->parameters(['todo-list' => 'list']);

Route::get('task', [TaskController::class, 'index'])->name('task.index');

Route::post('task', [TaskController::class, 'store'])->name('task.store');
