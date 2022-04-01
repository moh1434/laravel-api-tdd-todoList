<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Label;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

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
        $label = Label::factory()->raw();
        $this->authUser();
        $this->postJson(route('label.store'), $label)
            ->assertCreated();

        $this->assertDatabaseHas('labels', $label);
    }

    public function test_user_can_delete_a_label()
    {
        $label = $this->createLabel();

        $this->deleteJson(route('label.destroy', $label->id))->assertNoContent();

        $this->assertDatabaseMissing('labels', [
            'title' => $label->title
        ]);
    }
}
