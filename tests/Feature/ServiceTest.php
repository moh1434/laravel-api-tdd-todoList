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
            'token' => '"{\"access_token\":\"fake-token\"}"'
        ]);

        // $this->assertNotNull($this->user->services->first()->token);
    }

    public function test_data_of_a_week_can_be_stored_on_google_drive()
    {
        $this->createTask(['created_at' => now()->subDays(2)]);
        $this->createTask(['created_at' => now()->subDays(3)]);
        $this->createTask(['created_at' => now()->subDays(4)]);
        $this->createTask(['created_at' => now()->subDays(6)]);

        $this->createTask(['created_at' => now()->subDays(10)]);
        //

        $this->mock(Client::class, function (MockInterface $mock) {
            $mock->shouldReceive('setAccessToken')->once();
            $mock->shouldReceive('getLogger->info')->once();
            $mock->shouldReceive('shouldDefer')->once();
            $mock->shouldReceive('execute')->once();
        });

        $service = $this->createService();

        $this->postJson(route('service.store', $service->id))
            ->assertCreated();
    }
}
