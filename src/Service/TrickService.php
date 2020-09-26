<?php

namespace App\Service;

use App\Service\FileUploader;
use Doctrine\ORM\EntityManagerInterface;

class TrickService
{
    private $manager;

    private $fileUploader;

    public function __construct(EntityManagerInterface $manager, FileUploader $fileUploader)


    {
        $this->manager = $manager;
        $this->fileUploader = $fileUploader;
    }

    public function createTrick($trick, $user, $imageFile)
    {


        if ($imageFile) {
            $imageFileName = $this->fileUploader->upload($imageFile);
            $trick->setCoverImage($imageFileName);
        }

        foreach ($trick->getImages() as $image) {
            $fileName = $this->fileUploader->upload($image->getFile());
            $image->setPath($fileName);
            $image->setTrick($trick);

            $this->manager->persist($image);
        }

        foreach ($trick->getVideos() as $video) {
            $video->setTrick($trick);
            $this->manager->persist($video);
        }


        $trick->setUser($user);
        $this->manager->persist($trick);
        $this->manager->flush();
    }

    public function updateTrick($trick, $imageFile)
    {
        if ($imageFile) {

            $this->fileUploader->removeFile($trick->getCoverImage());
            $imageFileName = $this->fileUploader->upload($imageFile);
            $trick->setCoverImage($imageFileName);
        }


        foreach ($trick->getImages() as $image) {
            $file = $image->getFile();
            if ($file) {
                if ($image->getPath()) {
                    $this->fileUploader->removeFile($image->getPath());
                }
                $fileName = $this->fileUploader->upload($image->getFile());
                $image->setPath($fileName);
                $image->setTrick($trick);

                $this->manager->persist($image);
            }
        }


        foreach ($trick->getVideos() as $video) {
            $video->setTrick($trick);
            $this->manager->persist($video);
        }

        $this->manager->persist($trick);
        $this->manager->flush();
    }

    public function deleteTrick($trick){

        $this->manager->remove($trick);
        $this->manager->flush();

        $this->fileUploader->removeFile($trick->getCoverImage());
        $images = $trick->getImages();
        foreach ($images as $image) {
            $this->fileUploader->removeFile($image->getPath());
        }
    }

    public function createComment($comment, $trick, $user){

        $comment->setTrick($trick)
                ->setUser($user);
            $this->manager->persist($comment);
            $this->manager->flush();
    }
}
