<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Twilio\Rest\Client as TwilioClient;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\TwilioService;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Foundation\Auth\EmailVerificationRequest;


class EmailVerificationController extends Controller
{

    public function show()
    {
        return response('Please verify your email to continue', Response::HTTP_UNAUTHORIZED);
    }

    public function send(Request $request)
    {
        $request->user()->sendEmailVerificationNotification();

        return response('Verification link sent to your email', Response::HTTP_OK);
    }

    public function verify(Request $request, TwilioService $twilioService)
    {
        $request->validate([
            'token' => ['required']
        ]);

        $verification_check = $twilioService->checkVerification($request->token);

        // Check if the verify token is valid
        if ($verification_check->status === 'approved') {

            Auth::user()->markEmailAsVerified();

            return response('Email verified', Response::HTTP_OK);
        }

        return response('Email not verified, may be token expired', Response::HTTP_UNAUTHORIZED);
    }
}
