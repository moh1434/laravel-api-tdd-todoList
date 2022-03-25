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

    private $list;
    public function setUp(): void
    {
        parent::setUp();
        $this->list = TodoList::factory()->create();
    }
    public function test_fetch_todo_list()
    {


        $response = $this->getJson(route('todo-list.index'));


        $this->assertEquals(1, count($response->json()));
    }

    public function test_fetch_single_todo_list()
    {
        // action
        $response = $this->getJson(route('todo-list.show', $this->list->id))
            ->assertStatus(200);

        // assertion

        $this->assertEquals($response->json()['name'], $this->list->name);
    }
}
