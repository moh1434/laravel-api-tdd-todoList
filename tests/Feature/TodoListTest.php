<?php
// ./vendor/bin/phpunit
namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class TodoListTest extends TestCase
{
    public function test_example()
    {
        $this->withoutExceptionHandling();


        $response = $this->getJson(route('todo-list.index'));


        $this->assertEquals(1, count($response->json()));
    }
}
