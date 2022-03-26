<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\TodoListController;

Route::apiResource('todo-list', TodoListController::class)->parameters(['todo-list' => 'list']);

Route::apiResource('todo-list.task', TaskController::class)->parameters(['todo-list' => 'list'])
    ->except('show')->shallow();
