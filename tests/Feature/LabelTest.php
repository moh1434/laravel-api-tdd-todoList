<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class LabelTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        $user = $this->authUser();
    }

    public function test_user_can_create_new_label()
    {
        $this->postJson(route('label.store'), ['title' => 'my title', 'color' => 'red'])
            ->assertCreated();

        $this->assertDatabaseHas('labels', ['title' => 'my title', 'color' => 'red']);
    }
}
