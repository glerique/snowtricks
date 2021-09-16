<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\PasswordForgot;
use App\Entity\PasswordUpdate;
use App\Form\RegistrationType;
use App\Service\AccountService;
use App\Form\PasswordForgotType;
use App\Form\PasswordUpdateType;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;


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

    public function register(Request $request, AccountService $accountService)
    {
        $user = new User;

        $form = $this->createForm(RegistrationType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isvalid()) {


            $image = $form->get('avatar')->getData();

            $accountService->createUser($user, $image);

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

    public function forgotPassword(Request $request, UserRepository $repository, AccountService $accountService)
    {

        //creation du formulaire avec PasswordForgotType
        $passwordForgot = new PasswordForgot();
        $form = $this->createForm(PasswordForgotType::class, $passwordForgot);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {


            $email = $passwordForgot->getEmail();
            //Verification que l'adresse est dans la base de donnée
            $user = $repository->findOneByEmail($email);

            if ($user == false) {
                $this->addFlash('danger', 'aucun utilisateur correspondant');
            } else {

                $accountService->forgot($user);

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

    public function updatePassword(Request $request, UserRepository $repository, $nickname, $token, AccountService $accountService)
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

            $accountService->passwordUpdate($newPassword, $user);

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
     * @return RedirectResponse
     */
    public function AccountValidation(UserRepository $repository, $nickname, $token, AccountService $accountService)
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

        $accountService->activateUser($user);

        $this->addFlash(
            'success',
            "Votre compte a été activé avec succès !"
        );
        return $this->redirectToRoute('account_login');
    }
}
