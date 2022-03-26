<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\TodoList;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TaskController extends Controller
{
    public function index(TodoList $list)
    {
        $tasks = $list->tasks;
        return response($tasks);
    }

    public function store(Request $request, TodoList $list)
    {
        $task = $list->tasks()->create($request->all());
        return $task;
    }

    public function destroy(Task $task)
    {
        $task->delete();
        return response('', Response::HTTP_NO_CONTENT);
    }

    public function update(Request $request, Task $task)
    {
        $task->update($request->all());
        return response($task);
    }
}
