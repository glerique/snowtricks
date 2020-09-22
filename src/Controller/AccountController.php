<?php

namespace App\Controller;

use App\Entity\User;
use App\Service\FileUploader;
use App\Entity\PasswordForgot;
use App\Entity\PasswordUpdate;
use App\Form\RegistrationType;
use App\Form\PasswordForgotType;
use App\Form\PasswordUpdateType;
use Symfony\Component\Mime\Email;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AccountController extends AbstractController
{
    /**
     * Afficher la page de login
     * 
     * @Route("/account/login", name="account_login")
     * 
     * @return Response
     */
    public function login(AuthenticationUtils $utils)
    {
        $error = $utils->getLastAuthenticationError();

        return $this->render(
            'account/login.html.twig',
            ['hasError' => $error !== null]
        );
    }


    /**
     * Se deconnecter de l'application
     * @Route("/account/logout", name="account_logout")
     * 
     * @return void
     */
    public function logout()
    {
    }

    /**
     * Afficher le formulaire d'inscription
     * 
     * @Route("/account/register", name="account_register")
     * 
     * @return Response
     */

    public function register(Request $request, EntityManagerInterface $manager, UserPasswordEncoderInterface $encoder, MailerInterface $mailer, FileUploader $fileUploader)
    {
        $user = new User;

        $form = $this->createForm(RegistrationType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isvalid()) {
            $password = $encoder->encodePassword($user, $user->getPassword());
            $user->setPassword($password);
            $user->setToken($this->generateToken());      

            


            $image = $form->get('avatar')->getData();
            /*    
            $avatar = $user->setFile($image);
            $avatar = $imageUploader->uploadAvatar($avatar);
            $manager->persist($avatar);  
            */
            if ($image) {
                $imageFileName = $fileUploader->upload($image);
                $user->setAvatar($imageFileName);
            }


            $manager->persist($user);
            $manager->flush();


            $nickname = $user->getNickname();
            $token = $user->getToken();
            $url = "http://localhost:8000/account/activation/$nickname/$token";
            $email = (new Email())
                ->from('no-reply@example.com')
                ->to($user->getEmail())
                ->subject("Snowtricks - Finalisation de l'inscription")
                ->html('<h3>Bienvenue sur Snowtricks!</h3>
                    <p>Pour finaliser votre inscription, cliquez sur le lien suivant: 
                    <a href="' . $url . '">Finaliser l\'inscription</a></p>');

            $mailer->send($email);

            $this->addFlash(
                'success',
                "L'utilisateur a bien été enregistré. Un mail vous a été envoyé pour finaliser votre inscription. Cliquez sur le lien contenu dans cet e-mail pour valider votre compte."
            );

            
            return $this->redirectToRoute('account_login');
        }
        return $this->render('account/registration.html.twig', [
            'form' => $form->createView()
        ]);
    }
    /**
     * Demander un nouveau mot de passe
     * 
     * @Route("/account/forgot", name="account_forgot")
     * 
     * @return Response
     */
    
    public function forgotPassword( Request $request, UserRepository $repository,  EntityManagerInterface $manager, MailerInterface $mailer){

        //creation du formulaire avec PasswordForgotType
        $passwordForgot = new PasswordForgot();
        $form = $this->createForm(PasswordForgotType::class, $passwordForgot);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

           
            $email = $passwordForgot->getEmail();
            //Verification que l'adresse est dans la base de donnée
            $user = $repository->findOneByEmail($email);

            if($user == false){
                $this->addFlash('danger', 'aucun utilisateur correspondant');
            }else{

                $nickname = $user->getNickname();                
                // Génération d'un nouveau token pour plus de sécurité
                $user->setToken($this->generateToken());           
                // Update de la base de donnée avec le nouveau Token
                $token = $user->getToken();
                $manager->persist($user);
                $manager->flush();
               
                $url = "http://localhost:8000/account/update/$nickname/$token";
                $email = (new Email())
                    ->from('no-reply@example.com')
                    ->to($user->getEmail())
                    ->subject("Snowtricks - Réinitialisation du mot de passe")
                    ->html('<h3>Snowtricks - Mot de passe oublié</h3>
                        <p>Pour choisir un nouveau mot de passe, cliquez sur le lien suivant: 
                        <a href="'.$url.'" class="alert-link">Redéfinir le mot de passe</a></p>');
                $mailer->send($email);

                $this->addFlash('success', 'Un e-mail vous a été envoyé. Cliquez sur le lien contenu dans cet e-mail pour redéfinir votre mot de passe');
                return $this->redirectToRoute('account_login');
            }
        }

        return $this->render('account/forgot.html.twig', [
            'form' => $form->createView()
        ]);
    }
    
    
    /**
     *Modifier le mot de passe
     *
     *@Route("/account/update/{nickname}/{token}", name="account_update")     *
     * 
     *@return Response 
     */

    public function updatePassword(Request $request, UserRepository $repository, $nickname, $token, UserPasswordEncoderInterface $encoder,  EntityManagerInterface $manager)
    {
        $passwordUpdate = new PasswordUpdate();

        $user = $repository->findOneByNickname($nickname);

        
        if (!$user) {
            $this->addFlash(
                'danger',
                "Le compte que vous voulez valider n'existe pas."
            );
            return $this->redirectToRoute('account_login');
        }
        if ($token != $user->getToken()) {
            $this->addFlash(
                'danger',
                "Le Token n'est pas valide."
            );
            return $this->redirectToRoute('account_login');
        }

        $form = $this->createForm(PasswordUpdateType::class, $passwordUpdate);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            
                $newPassword = $passwordUpdate->getNewPassword();
                $password = $encoder->encodePassword($user, $newPassword);

                $user->setPassword($password);

                $manager->persist($user);
                $manager->flush();

                $this->addFlash(
                    'success',
                    "Votre mot de passe a bien été modifié !"
                );

                return $this->redirectToRoute('account_login');
            }
        
        return $this->render('account/update.html.twig', [
            'form' => $form->createView()
        ]);
    }
    

    /**
     * @Route("account/activation/{nickname}/{token}", name="account_activation")
     *
     * @param UserRepository $repo
     * @param $email
     * @param $token
     * @param EntityManagerInterface $manager
     * @return RedirectResponse
     */
    public function AccountValidation(UserRepository $repository, $nickname, $token, EntityManagerInterface $manager)
    {
        $user = $repository->findOneByNickname($nickname);


        if (!$user) {
            $this->addFlash(
                'danger',
                "Le compte que vous voulez valider n'existe pas."
            );
            return $this->redirectToRoute('account_login');
        }
        if ($token != $user->getToken()) {
            $this->addFlash(
                'danger',
                "Le Token n'est pas valide."
            );
            return $this->redirectToRoute('account_login');
        }
                
        $user->setValidated("1");
        $manager->persist($user);
        $manager->flush();

        $this->addFlash(
            'success',
            "Votre compte a été activé avec succès !"
        );
        return $this->redirectToRoute('account_login');
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
