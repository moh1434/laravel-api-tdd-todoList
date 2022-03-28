<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_user_can_register()
    {
        $this->postJson(route('user.register'), [
            'name' => 'moh1434',
            'email' => 'moh1434.ma@gmail.com',
            'password' => '12345678'
        ])->assertCreated();

        $this->assertDatabaseHas('users', ['name' => 'moh1434']);
    }
}
