<?php

namespace App\Http\Controllers;

use App\Http\Requests\TodoListRequest;
use App\Models\TodoList;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TodoListController extends Controller
{
    public function index()
    {
        $lists = TodoList::all();
        return response($lists);
    }
    public function show(TodoList $list)
    {
        return response($list);
    }

    public function store(TodoListRequest $request)
    {
        $list = TodoList::create($request->all());
        return $list;
    }
    public function update(TodoListRequest $request, TodoList $list)
    {
        $list->update($request->all());
        return $list;
    }

    public function destroy(TodoList $list)
    {

        $list->delete();
        return response('', Response::HTTP_NO_CONTENT);
    }
}
