<?php

namespace App\Services;

use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class UploaderService
{
    // On va lui passer un objet de type UploadFile
    // Elle doit nous retourner le nom de ce fichier
    
    public function __construct(private SluggerInterface $slugger)
    {
    }

    public function uploadImage(
        UploadedFile $file,
        string $directoryFolder
    ){
        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $this->slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$file->guessExtension();

                // Move the file to the directory where files are stored
                try {
                    $file->move(
                        $directoryFolder,
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }
                return $newFilename;
    }
}