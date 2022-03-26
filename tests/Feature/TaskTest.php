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
        $task = Task::factory()->create();

        $response = $this->getJson(route('task.index'))->assertStatus(200)->json();

        $this->assertEquals(1, count($response));
        $this->assertEquals($task->title, $response[0]['title']);
    }

    public function test_store_a_task_for_a_todo_list()
    {
        $task = Task::factory()->make();
        $this->postJson(route('task.store'), ['title' => $task->title])
            ->assertStatus(Response::HTTP_CREATED);

        $this->assertDatabaseHas('tasks', ['title' => $task->title]);
    }
}
