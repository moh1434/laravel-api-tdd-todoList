<?php

use Google\Client;
use Google\Service\Drive;
use Google\Service\Drive\DriveFile;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});


Route::get('drive', function () {
    $client = new Client;
    $client->setClientId('443633211637-pk474jnmm4pa3dlji1hhi7bgaevtu68q.apps.googleusercontent.com');
    $client->setClientSecret('5MqXkjuOmYafJOFZQovPzA9M');
    $client->setRedirectUri('http://127.0.0.1:8000/google-drive/callback');

    $client->setScopes([
        'https://www.googleapis.com/auth/drive',
        'https://www.googleapis.com/auth/drive.file'
    ]);

    $url = $client->createAuthUrl();
    return redirect($url);
});


Route::get('/google-drive/callback', function () {
    return Request('code');

    $client = new Client;
    $client->setClientId('443633211637-pk474jnmm4pa3dlji1hhi7bgaevtu68q.apps.googleusercontent.com');
    $client->setClientSecret('5MqXkjuOmYafJOFZQovPzA9M');
    $client->setRedirectUri('http://127.0.0.1:8000/google-drive/callback');
    $code = Request('code');

    $access_token = $client->fetchAccessTokenWithAuthCode($code);
    return $access_token;
});


Route::get('upload', function () {
    $client = new Client;
    $access_token = 'ya29.A0ARrdaM94IuL9JFwFVCUndBQRe5hhEg3UW6whM4wg1afbmV2PmVZUD9N2MIZc3J032IhQ_zQ5Hm_jD_PSGW0jgK1wUSlfY8e-s_gQ6MQTISg0hSxOc8EgFap4vuQY-lD2tTWv64TZ4DKaWi98p9PTXkT8ZzhS';

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
    //
});
