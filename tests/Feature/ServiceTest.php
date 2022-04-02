<?php

namespace Tests\Feature;

use Google\Client;
use Tests\TestCase;
use Mockery\MockInterface;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ServiceTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = $this->authUser();
    }

    public function test_a_user_can_connect_to_a_service_and_token_is_stored()
    {
        $this->mock(Client::class, function (MockInterface $mock) {
            // $mock->shouldReceive('setClientId')->once();
            $mock->shouldReceive('setScopes')->once();
            $mock->shouldReceive('createAuthUrl')
                ->once()
                ->andReturn('http://localhost');
        });

        $response = $this->getJson(route('service.connect', 'google-drive'))
            ->assertOk()
            ->json();

        $this->assertEquals($response['url'], 'http://localhost');
    }

    public function test_service_callback_will_store_token()
    {
        $this->mock(Client::class, function (MockInterface $mock) {
            // $mock->shouldReceive('setClientId')->once();
            // $mock->shouldReceive('setClientSecret')->once();
            // $mock->shouldReceive('setRedirectUri')->once();
            $mock->shouldReceive('fetchAccessTokenWithAuthCode')
                ->once()
                ->andReturn('fake-token');
        });
        $response = $this->postJson(route('service.callback'), ['code' => 'Dummy code'])
            ->assertCreated();

        $this->assertDatabaseHas('services', [
            'user_id' => $this->user->id,
            'token' => '{"access_token":"fake-token"}'
        ]);

        // $this->assertNotNull($this->user->services->first()->token);
    }
}
