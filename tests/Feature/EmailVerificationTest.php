<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Mockery\MockInterface;
use Laravel\Sanctum\Sanctum;
use Twilio\Rest\Client as TwilioClient;
use Illuminate\Foundation\Testing\WithFaker;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Twilio\Rest\Verify\V2\Service\VerificationCheckInstance;

class EmailVerificationTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        $this->user = $this->authUser(['email_verified_at' => null]);
    }

    public function test_a_user_redirected_and_forbidden_if_accessed_guarded_route_before_verifying_its_email()
    {
        $this->withExceptionHandling();
        $user = User::factory(['email_verified_at' => null])->create();
        Sanctum::actingAs($user);

        //This route are guarded by 'verified' middleware
        $this->getJson(route('todo-list.index'))->assertStatus(Response::HTTP_FORBIDDEN);


        $user->email_verified_at = now();

        $this->getJson(route('todo-list.index'))->assertStatus(Response::HTTP_OK);
    }

    public function test_user_can_request_email_verification()
    {
        $this->mock(TwilioClient::class, function (MockInterface $mock) {
            $mock->shouldReceive('request->getStatusCode')->andReturn('200');
            $mock->shouldReceive('request->getContent')->andReturn([]);

            // $mock->shouldReceive('verify->v2->services->verifications->create')->once();
        });
        $response = $this->postJson(route('verification.send'))
            ->assertStatus(Response::HTTP_OK);

        $this->assertEquals($response->getContent(), 'Verification link sent to your email');
    }


    public function test_an_email_can_be_verificated_with_valid_token()
    {

        $this->mock(TwilioClient::class, function (MockInterface $mock) {
            $mock->shouldReceive('request->getStatusCode')
                ->andReturn('200');

            //status=>'approved' means its 'valid token'
            $mock->shouldReceive('request->getContent')
                ->andReturn(['status' => 'approved']);
        });

        $this->getJson(route('verification.verify', ['token' => 'valid token']))
            ->assertOk();

        $this->assertTrue($this->user->hasVerifiedEmail());

        $this->assertDatabaseHas('users', [
            'id' => $this->user->id,
            'email_verified_at' => $this->user->email_verified_at
        ]);
    }

    public function test_an_email_can_not_be_verificated_with_invalid_token()
    {
        $this->mock(TwilioClient::class, function (MockInterface $mock) {
            $mock->shouldReceive('request->getStatusCode')
                ->andReturn('200');

            //status=>null means its 'invalid token'
            $mock->shouldReceive('request->getContent')
                ->andReturn(['status' => null]);
        });

        $this->getJson(route('verification.verify', ['token' => 'invalid token']))
            ->assertUnauthorized();

        $this->assertFalse($this->user->hasVerifiedEmail());

        $this->assertDatabaseHas('users', [
            'id' => $this->user->id,
            'email_verified_at' => null
        ]);
    }
}
