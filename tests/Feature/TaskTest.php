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
	/**
	 * A basic feature test example.
	 *
	 * @return void
	 */
	public function test_fetch_all_tasks_of_a_todo_list()
	{
		$list = $this->createTodoList();
		$task = $this->createTask();

		$response = $this->getJson(route('todo-list.task.index', $list->id))->assertStatus(200)->json();

		$this->assertEquals(1, count($response));
		$this->assertEquals($task->title, $response[0]['title']);
	}

	public function test_store_a_task_for_a_todo_list()
	{
		$list = $this->createTodoList();
		$task = Task::factory()->make();

		$this->postJson(route('todo-list.task.store', $list->id), ['title' => $task->title])
			->assertStatus(Response::HTTP_CREATED);

		$this->assertDatabaseHas('tasks', [
			'title' => $task->title,
			'todo_list_id' => $list->id
		]);
	}

	public function test_delete_a_task_from_database()
	{
		$task = $this->createTask();

		$this->deleteJson(route('task.destroy', $task->id))
			->assertStatus(Response::HTTP_NO_CONTENT);

		$this->assertDatabaseMissing('tasks', ['title' => $task->title]);
	}

	public function test_update_a_task_of_a_todo_list()
	{
		$task = $this->createTask();

		$response = $this->patchJson(route('task.update', $task->id), ['title' => 'updated title'])
			->assertStatus(200)->json();
		$this->assertDatabaseHas('tasks', ['title' => 'updated title']);
	}
}
