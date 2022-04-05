<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\TodoList;
use Illuminate\Http\Request;
use App\Http\Requests\TaskRequest;
use App\Http\Resources\TaskResource;
use Symfony\Component\HttpFoundation\Response;

class TaskController extends Controller
{
    public function index(TodoList $list)
    {
        $tasks = $list->tasks;
        return TaskResource::collection($tasks);
    }

    public function store(TaskRequest $request, TodoList $list)
    {
        $task = $list->tasks()->create($request->validated());
        return new TaskResource($task);
    }

    public function destroy(Task $task)
    {
        $task->delete();
        return response('', Response::HTTP_NO_CONTENT);
    }

    public function update(Request $request, Task $task)
    {
        $task->update($request->all());
        return new TaskResource($task);
    }
}
