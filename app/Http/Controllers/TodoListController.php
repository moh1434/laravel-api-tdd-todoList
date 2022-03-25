<?php

namespace App\Http\Controllers;

use App\Models\TodoList;
use Illuminate\Http\Request;

class TodoListController extends Controller
{
    public function index()
    {
        $lists = TodoList::all();
        return response($lists);
    }
    // TodoList $todoList
    public function show($id)
    {
        $list = TodoList::findOrFail($id);
        return response($list);
    }
}
