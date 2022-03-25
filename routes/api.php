<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TodoListController;

Route::get('todo-list', [TodoListController::class, 'index'])->name('todo-list.index'); // No
Route::get('todo-list/{list}', [TodoListController::class, 'show'])
    ->name('todo-list.show');
