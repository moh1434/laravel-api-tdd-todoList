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
        $this->list = TodoList::factory()->create(['name' => 'my list']);
    }
    public function test_fetch_todo_list()
    {


        $response = $this->getJson(route('todo-list.index'));


        $this->assertEquals(1, count($response->json()));
        $this->assertEquals('my list', $response->json()[0]['name']);
    }

    public function test_fetch_single_todo_list()
    {
        // action
        $response = $this->getJson(route('todo-list.show', $this->list->id))
            ->assertStatus(200);

        // assertion

        $this->assertEquals($response->json()['name'], $this->list->name);
    }

    public function test_store_new_todo_list()
    {
        $list = TodoList::factory()->make(['name' => 'my list in db']);
        $response = $this->postJson(route('todo-list.store'), ['name' => $list->name])
            ->assertStatus(201)
            ->json();

        $this->assertEquals('my list in db', $response['name']);

        $this->assertDatabaseHas('todo_lists', ['name' => $list->name]);
    }

    public function test_while_storing_todo_list_name_field_is_required()
    {
        $this->withExceptionHandling();
        $this->postJson(route('todo-list.store'))
            ->assertStatus(422)
            ->assertJsonValidationErrors(['name']);
    }
}
