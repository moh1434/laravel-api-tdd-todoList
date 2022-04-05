<?php

namespace App\Http\Controllers;

use auth;
use App\Models\TodoList;
use Illuminate\Http\Request;
use App\Http\Requests\TodoListRequest;
use App\Http\Resources\TodoListResource;
use Symfony\Component\HttpFoundation\Response;

class TodoListController extends Controller
{
    public function index()
    {
        $lists = auth()->user()->todo_lists;
        return TodoListResource::collection($lists);
    }
    public function show(TodoList $list)
    {
        return new TodoListResource($list);
    }

    public function store(TodoListRequest $request)
    {
        $list = auth()->user()
            ->todo_lists()
            ->create($request->validated());

        return new TodoListResource($list);
    }
    public function update(TodoListRequest $request, TodoList $list)
    {
        $list->update($request->all());
        return new TodoListResource($list);;
    }

    public function destroy(TodoList $list)
    {

        $list->delete();
        return response('', Response::HTTP_NO_CONTENT);
    }
}
