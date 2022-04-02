<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ServiceTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        $this->authUser();
    }

    public function test_a_user_can_connect_to_a_service_and_token_is_stored()
    {
        $response = $this->getJson(route('service.connect', 'google-drive'))
            ->assertOk()
            ->json();

        $this->assertNotNull($response['url']);
    }
}
