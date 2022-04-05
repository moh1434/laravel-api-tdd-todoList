<?php

namespace App\Services;

use ZipArchive;

class Zipper
{
    public static function createZipOf(String $fileName)
    {

        $zip = new ZipArchive();
        $zipFileName = storage_path('app/public/temp/' . now()->timestamp . '-task.zip');

        if ($zip->open($zipFileName, ZipArchive::CREATE) === true) {
            $filePath = storage_path('app/public/temp/') . $fileName;
            $zip->addFile($filePath, $fileName);
        }
        $zip->close();


        return $zipFileName;
    }
}
