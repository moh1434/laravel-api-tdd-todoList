<?php

namespace App\Http\Controllers;

use Google\Client;
use App\Models\Task;
use App\Models\Service;
use Google\Service\Drive;
use Illuminate\Http\Request;
use Google\Service\Drive\DriveFile;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;
use ZipArchive;

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
        $tasks = Task::where('created_at', '>=', now()->subDays(7))
            ->get();

        $jsonFileName = 'task_dump.json';
        Storage::put("/public/temp/$jsonFileName", $tasks->toJson());

        $zip = new ZipArchive();
        $zipFileName = storage_path('app/public/temp/' . now()->timestamp . '-task.zip');

        if ($zip->open($zipFileName, ZipArchive::CREATE) === true) {
            $filePath = storage_path('app/public/temp/') . $jsonFileName;
            $zip->addFile($filePath);
        }
        $zip->close();


        $access_token = $service->token['access_token'];

        $client->setAccessToken($access_token);


        $service = new Drive($client);
        $file = new DriveFile();

        $file->setName("Hello_World.zip");
        $result2 = $service->files->create(
            $file,
            array(
                'data' => file_get_contents($zipFileName),
                'mimeType' => 'application/octet-stream',
                'uploadType' => 'multipart'
            )
        );

        return response('Uploaded', Response::HTTP_CREATED);
    }
}
