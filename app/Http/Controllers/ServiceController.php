<?php

namespace App\Http\Controllers;

use Google\Client;
use App\Models\Task;
use App\Models\Service;
use App\Services\Zipper;
use Illuminate\Http\Request;
use App\Services\GoogleDrive;
use App\Http\Resources\TaskResource;
use Illuminate\Support\Facades\Storage;
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
            'token' => $access_token,
            'name' => 'google-drive'
        ]);

        return $service;
    }
    public function store(Service $service, GoogleDrive $drive)
    {
        $tasks = Task::where('created_at', '>=', now()->subDays(7))
            ->get();

        $jsonFileName = 'task_dump.json';
        Storage::put("/public/temp/$jsonFileName", TaskResource::collection($tasks)->toJson());


        $zipFileName = Zipper::createZipOf($jsonFileName);

        $drive->uploadFile($zipFileName, $service->token['access_token']);

        Storage::deleteDirectory('public/temp');

        return response('Uploaded', Response::HTTP_CREATED);
    }
}
