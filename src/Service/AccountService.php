<?php

namespace App\Service;

use App\Entity\User;
use App\Service\FileUploader;
use App\Service\MailerService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;


class AccountService
{

    private $manager;

    private $fileUploader;

    private $encoder;

    private $mailerService;

    public function __construct(EntityManagerInterface $manager, FileUploader $fileUploader, MailerService $mailerService,  UserPasswordEncoderInterface $encoder)


    {
        $this->manager = $manager;
        $this->fileUploader = $fileUploader;
        $this->mailerService = $mailerService;
        $this->encoder = $encoder;
    }

    public function createUser($user, $image)
    {

        $password = $this->encoder->encodePassword($user, $user->getPassword());
        $user->setPassword($password);
        $user->setToken($this->generateToken());



        if ($image) {
            $imageFileName = $this->fileUploader->upload($image);
            $user->setAvatar($imageFileName);
        }


        $this->manager->persist($user);
        $this->manager->flush();


        $nickname = $user->getNickname();
        $token = $user->getToken();

        $this->mailerService->registerMail($nickname, $token, $user);
    }


    public function forgot(User $user)
    {


        $user->setToken($this->generateToken());
        // Update de la base de donnée avec le nouveau Token
        $nickname = $user->getNickname();


        // Génération d'un nouveau token pour plus de sécurité
        $token = $user->getToken();
        $this->manager->persist($user);
        $this->manager->flush();

        $this->mailerService->forgotMail($user, $nickname, $token);
    }

    public function passwordUpdate($newPassword, $user)
    {


        $password = $this->encoder->encodePassword($user, $newPassword);

        $user->setPassword($password);

        $this->manager->persist($user);
        $this->manager->flush();
    }

    public function activateUser($user)
    {

        $user->setValidated("1");

        $this->manager->persist($user);
        $this->manager->flush();
    }

    /**
     * Génération du token de validation d'incription
     *
     * @return string
     */
    public function generateToken()
    {

        return md5(bin2hex(openssl_random_pseudo_bytes(6)));
    }
}
