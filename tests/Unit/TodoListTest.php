<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TodoListTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function test_a_todo_list_can_has_many_tasks()
    {
        $list = $this->createTodoList();
        $task = $this->createTask(['todo_list_id' => $list->id]);

        $this->assertInstanceOf(Task::class, $list->tasks->first());
    }
}
