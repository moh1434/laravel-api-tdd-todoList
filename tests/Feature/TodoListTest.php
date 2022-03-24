<?php
// ./vendor/bin/phpunit
namespace Tests\Feature;

use Tests\TestCase;
use App\Models\TodoList;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TodoListTest extends TestCase
{
    use RefreshDatabase;

    public function test_fetch_todo_list()
    {
        TodoList::create(['name' => 'my list']);

        $response = $this->getJson(route('todo-list.index'));


        $this->assertEquals(1, count($response->json()));
    }
}
