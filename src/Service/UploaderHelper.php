<?php


namespace App\Service;


use Symfony\Component\HttpFoundation\File\UploadedFile;

class UploaderHelper
{
    private $uploadsPath;
    public function __construct(string $uploadsPath)
    {
        $this->uploadsPath = $uploadsPath;
    }

    public function uploadImage(UploadedFile $uploadedFile):string
    {
        $originalFilename = pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME);
        $fileRename=$originalFilename.'_'.md5(uniqid()).'.'.$uploadedFile->guessExtension();
        $uploadedFile->move($this->uploadsPath, $fileRename);

        return $fileRename;
    }
}