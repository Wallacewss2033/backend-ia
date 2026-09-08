<?php

namespace App\Traits;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Kreait\Laravel\Firebase\Facades\Firebase;

trait UploadsImagesToFirebase
{
    public function uploadImageToFirebase(UploadedFile $file, string $type, string $folder = 'uploads'): array
    {
        if ($type === 'favicon') {
            $imageInfo = getimagesize($file->getPathname());
            $width = $imageInfo[0];
            $height = $imageInfo[1];
            
            if ($width !== $height) {
                throw new \Exception('O favicon deve ter formato quadrado (proporção 1:1).');
            }
            if ($width > 512 || $height > 512) {
                throw new \Exception('O tamanho do favicon deve ser de no máximo 512x512 pixels.');
            }
        }

        $extension = $file->getClientOriginalExtension();
        $filename = Str::uuid() . '.' . $extension;
        $firebasePath = trim($folder, '/') . '/' . $type . '/' . $filename;

        $storage = app('firebase.storage');
        $bucket = $storage->getBucket();

        $bucket->upload(
            fopen($file->getPathname(), 'r'),
            [
                'name' => $firebasePath
            ]
        );

        $url = "https://firebasestorage.googleapis.com/v0/b/" . $bucket->name() . "/o/" . urlencode($firebasePath) . "?alt=media";

        return [
            'url' => $url,
            'path' => $firebasePath
        ];
    }
}
