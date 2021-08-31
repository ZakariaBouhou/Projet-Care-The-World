<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\File\UploadedFile;

class ImageUploader
{
    public function upload(UploadedFile $image)
    {
        if($image !== null){
            $newFilename = uniqid() . '.' . $image->guessExtension();
            $image->move($_ENV['IMAGE_EVENT'], $newFilename);

            return $newFilename;
        }
        return null;
    }
}