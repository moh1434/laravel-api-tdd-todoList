<?php

namespace App\Http\Controllers;

use Google\Client;
use App\Models\Service;
use Google\Service\Drive;
use Illuminate\Http\Request;
use Google\Service\Drive\DriveFile;
use Symfony\Component\HttpFoundation\Response;

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
    public function store(Request $request, Service $service, Client $client)
    {
        $access_token = $service->token['access_token'];

        $client->setAccessToken($access_token);
        $service = new Drive($client);
        $file = new DriveFile();

        //
        DEFINE("TESTFILE", 'testfile-small.txt');
        if (!file_exists(TESTFILE)) {
            $fh = fopen(TESTFILE, 'w');
            fseek($fh, 1024 * 1024);
            fwrite($fh, "!", 1);
            fclose($fh);
        }
        //

        $file->setName("Hello World!");
        $result2 = $service->files->create(
            $file,
            array(
                'data' => file_get_contents(TESTFILE),
                'mimeType' => 'application/octet-stream',
                'uploadType' => 'multipart'
            )
        );

        return response('', Response::HTTP_CREATED);
    }
}
