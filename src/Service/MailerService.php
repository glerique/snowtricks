<?php

namespace App\Service;

use Symfony\Component\Mime\Email;
use Symfony\Component\Mailer\MailerInterface;

class MailerService
{

    private $mailer;

    public function __construct(MailerInterface $mailer)


    {
        $this->mailer = $mailer;
    }



    public function registerMail($nickname, $token, $user)
    {
        $url = "http://localhost:8000/account/activation/$nickname/$token";
        $email = (new Email())
            ->from('no-reply@example.com')
            ->to($user->getEmail())
            ->subject("Snowtricks - Finalisation de l'inscription")
            ->html('<h3>Bienvenue sur Snowtricks!</h3>
            <p>Pour finaliser votre inscription, cliquez sur le lien suivant: 
            <a href="' . $url . '">Finaliser l\'inscription</a></p>');

        $this->mailer->send($email);
    }

    public function forgotMail($user, $nickname, $token)
    {
        $url = "http://localhost:8000/account/update/$nickname/$token";
        $email = (new Email())
            ->from('no-reply@example.com')
            ->to($user->getEmail())
            ->subject("Snowtricks - Réinitialisation du mot de passe")
            ->html('<h3>Snowtricks - Mot de passe oublié</h3>
            <p>Pour choisir un nouveau mot de passe, cliquez sur le lien suivant: 
            <a href="' . $url . '" class="alert-link">Redéfinir le mot de passe</a></p>');

        $this->mailer->send($email);
    }
}
