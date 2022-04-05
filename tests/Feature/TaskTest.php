<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Task;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpFoundation\Response;

class TaskTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        $this->authUser();
    }

    public function test_fetch_all_tasks_of_a_todo_list()
    {
        $list = $this->createTodoList();
        $list2 = $this->createTodoList();
        $label = $this->createLabel(['user_id' => auth()->id()]);

        $task = $this->createTask([
            'todo_list_id' => $list->id,
            'label_id' => $label->id
        ]);

        $this->createTask(['todo_list_id' => $list2->id]);

        $response = $this->getJson(route('todo-list.task.index', $list->id))
            ->assertStatus(200)->json('data');

        $this->assertEquals(1, count($response));
        $this->assertEquals($task->title, $response[0]['title']);
    }

    public function test_store_a_task_for_a_todo_list()
    {
        $list = $this->createTodoList();
        $task = Task::factory()->make();
        // $label = $this->createLabel();

        $this->postJson(route('todo-list.task.store', $list->id), [
            'title' => $task->title,
            'label_id' => $task->label_id
        ])
            ->assertStatus(Response::HTTP_CREATED);

        $this->assertDatabaseHas('tasks', [
            'title' => $task->title,
            'todo_list_id' => $list->id,
            'label_id' => $task->label_id
        ]);
    }

    public function test_store_a_task_for_a_todo_list_without_a_label()
    {
        $list = $this->createTodoList();
        $task = Task::factory(['label_id' => null])->make();
        // $label = $this->createLabel();

        $this->postJson(route('todo-list.task.store', $list->id), [
            'title' => $task->title
        ])
            ->assertStatus(Response::HTTP_CREATED);

        $this->assertDatabaseHas('tasks', [
            'title' => $task->title,
            'todo_list_id' => $list->id,
            'label_id' => null
        ]);
    }

    public function test_delete_a_task_from_database()
    {
        $task = $this->createTask();

        $this->deleteJson(route('task.destroy', $task->id))
            ->assertStatus(Response::HTTP_NO_CONTENT);

        $this->assertDatabaseMissing('tasks', [
            'title' => $task->title
        ]);
    }

    public function test_update_a_task_of_a_todo_list()
    {
        $task = $this->createTask();

        $response = $this->patchJson(route('task.update', $task->id), [
            'title' => 'updated title'
        ])
            ->assertStatus(200)->json();
        $this->assertDatabaseHas('tasks', ['title' => 'updated title']);
    }
}
