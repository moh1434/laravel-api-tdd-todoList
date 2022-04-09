<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;
use Twilio\Rest\Client as TwilioClient;
use Symfony\Component\HttpFoundation\Response;

class TwilioService
{
    protected $twilio;

    public function __construct(TwilioClient $twilio)
    {
        $this->twilio = $twilio;
        $this->user = Auth::user();
    }

    public function sendEmailVerificationNotification()
    {
        $verifySID = config('services.twilio.verify_sid');
        $verification = $this->twilio->verify->v2->services($verifySID)
            ->verifications
            ->create($this->user->email, "email");
    }

    public function checkVerification(String $verification_token)
    {
        try {
            $verifySID = config('services.twilio.verify_sid');

            $verification_check = $this->twilio->verify->v2->services($verifySID)
                ->verificationChecks
                ->create($verification_token, ["to" => $this->user->email]);

            return $verification_check;
        } catch (\Exception $e) {
            // Redirect to elsewhere
            abort(Response::HTTP_UNAUTHORIZED, 'Email not verified, may be token expired');
        }
    }
}
