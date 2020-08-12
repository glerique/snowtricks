<?php

namespace App\Service;

use App\Entity\Image;
use Symfony\Component\String\Slugger\SluggerInterface;

class ImageUploader
{
    private $slugger;

    public function __construct(SluggerInterface $slugger)
    {
        $this->slugger = $slugger;
    }
    /**
     * specific service for image upload to use image object as parameter
     * @param Image $image
     * @return Image $image
     */
    public function uploadImage(Image $image): Image
    {
        $file = $image->getFile();
        if ($file !== null) {
            $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $safeFilename = $this->slugger->slug($originalFilename);
            $path = $safeFilename.'-'.uniqid().'.'.$file->guessExtension();
            $folder = 'uploads/images';
            $file->move($folder, $path);
            $image->setPath($path);            
        }
        
        return $image;
    }

}