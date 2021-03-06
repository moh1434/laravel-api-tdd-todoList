<?php
// ./vendor/bin/phpunit
namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\TodoList;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

class TodoListTest extends TestCase
{
    use RefreshDatabase;

    private $list;
    public function setUp(): void
    {
        parent::setUp();
        $user = $this->authUser();
        $this->list = $this->createTodoList([
            'name' => 'my list',
            'user_id' => $user->id
        ]);
    }
    public function test_fetch_all_todo_list_for_authenticated_user()
    {
        $this->createTodoList();
        $response = $this->getJson(route('todo-list.index'))->json('data');

        $this->assertEquals(1, count($response));
        $this->assertEquals('my list', $response[0]['name']);
    }

    public function test_fetch_single_todo_list()
    {
        // action
        $response = $this->getJson(route('todo-list.show', $this->list->id))
            ->assertStatus(200)
            ->json('data');

        // assertion

        $this->assertEquals($response['name'], $this->list->name);
    }

    public function test_store_new_todo_list()
    {
        $list = TodoList::factory()->make(['name' => 'my list in db']);
        $response = $this->postJson(route('todo-list.store'), ['name' => $list->name])
            ->assertStatus(201)
            ->json('data');

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

    public function test_delete_todo_list()
    {
        $this->deleteJson(route('todo-list.destroy', $this->list->id))
            ->assertNoContent();
        $this->assertDatabaseMissing('todo_lists', ['name' => $this->list->name]);
    }

    public function test_update_todo_list()
    {
        $this->patchJson(route('todo-list.update', $this->list->id), ['name' => 'updated name'])
            ->assertStatus(200);
        $this->assertDatabaseHas('todo_lists', ['id' => $this->list->id, 'name' => 'updated name']);
    }

    public function test_while_updating_todo_list_name_field_is_required()
    {
        $this->withExceptionHandling();
        $this->patchJson(route('todo-list.update', $this->list->id))
            ->assertStatus(422)
            ->assertJsonValidationErrors(['name']);
    }
}
