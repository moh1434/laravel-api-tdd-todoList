<?php

namespace App\Http\Controllers;

use Google\Client;
use App\Models\Service;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public const DRIVE_SCOPES = [
        'https://www.googleapis.com/auth/drive',
        'https://www.googleapis.com/auth/drive.file'
    ];
    public function connect(Request $request, Client $client)
    {
        if ($request->service != "google-drive") {
            return;
        }

        $client->setScopes(SELF::DRIVE_SCOPES);

        $url = $client->createAuthUrl();
        return response(['url' => $url]);
    }

    public function callback(Request $request, Client $client)
    {
        $access_token = $client->fetchAccessTokenWithAuthCode($request->code);

        $service = Service::create([
            'user_id' => auth()->id(),
            'token' => json_encode([
                'access_token' => $access_token
            ]),
            'name' => 'google-drive'
        ]);

        return $service;
    }
}
