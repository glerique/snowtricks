<?php

namespace App\Controller;


use App\Entity\Trick;
use App\Entity\Comment;
use App\Form\TrickType;
use App\Form\CommentType;
use App\Service\TrickService;
use App\Repository\ImageRepository;
use App\Repository\TrickRepository;
use App\Repository\VideoRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class TrickController extends AbstractController
{

    /**
     * Pour créer un trick
     *
     * @Route("/tricks/new", name="tricks_create")
     * @IsGranted("ROLE_USER")
     * 
     * @param Request $request     *   
     * @return Response
     */
    public function create(Request $request, TrickService $trickService)
    {
        $trick = new Trick();
        


        $form = $this->createForm(TrickType::class, $trick);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isvalid()) {

            $imageFile = $form->get('coverImage')->getData();
            
            $user = $this->getUser();

            $trickService->createTrick($trick, $user, $imageFile);

            

            $this->addFlash(
                'success',
                "Le trick {$trick->getName()} a bien été enregistré"
            );

            return $this->redirectToRoute('tricks_show', [
                'slug' => $trick->getSlug()
            ]);
        }
        return $this->render('trick/new.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * Pour modifier un trick
     * 
     * @Route("/tricks/{slug}/edit", name="tricks_edit")
     * @Security("is_granted('ROLE_USER')", message="Vous ne pouvez pas modifier ce Trick")
     * 
     * @param Trick $trick
     * @param Request $request     
     * @return Response
     */
    public function edit(Trick $trick, Request $request, TrickService $trickService)
    {


        $form = $this->createForm(TrickType::class, $trick);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isvalid()) {

            $imageFile = $form->get('coverImage')->getData();

            $trickService->updateTrick($trick, $imageFile);

            $this->addFlash(
                'success',
                "Le trick {$trick->getName()} a bien été modifié"
            );

            return $this->redirectToRoute('tricks_show', [
                'slug' => $trick->getSlug(),
            ]);
        }

        return $this->render('trick/edit.html.twig', [
            'form' => $form->createView(),
            'trick' => $trick
        ]);
    }


    /**
     * Pour afficher plusieurs tricks
     * 
     * @Route("/", name="tricks_index")
     * @param TrickRepository $repo 
     * @return Response
     */

    public function index(TrickRepository $repository)
    {
        $tricks = $repository->findBy([], ['createdAt' => 'ASC'], 9, 0);
        return $this->render('trick/index.html.twig', [
            'tricks' => $tricks,
        ]);
    }



    /**
     * @Route("/{start}", name="moreTricks", requirements={"start": "\d+"})
     * @param TrickRepository $repo
     * @param int $start
     * @return Response
     */
    public function moreTricks(TrickRepository $repo, $start = 9)
    {
        $tricks = $repo->findBy([], ['createdAt' => 'ASC'], 5, $start);

        return $this->render('trick/moreTricks.html.twig', [
            'tricks' => $tricks
        ]);
    }

    /**
     * Pour afficher un trick
     *
     * @Route("/tricks/{slug}", name="tricks_show")
     * 
     * @return Response
     */
    public function show(Request $request,  Trick $trick, TrickService $trickService, ImageRepository $repo_image, VideoRepository $repo_video)
    {

        $comment = new Comment();

        $form = $this->createForm(CommentType::class, $comment);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $user = $this->getUser();
            $trickService->createComment($comment, $trick, $user);

            $this->addFlash(
                'success',
                "Le commentaire a bien été enregistré"
            );

            return $this->redirectToRoute('tricks_show', [
                'slug' => $trick->getSlug()
            ]);
        }
        $images = $repo_image->findBy(['trick' => $trick->getId()]);
        $videos = $repo_video->findBy(['trick' => $trick->getId()]);

        return $this->render('trick/show.html.twig', [
            'form' => $form->createView(),
            'trick' => $trick,
            'images' => $images,
            'videos' => $videos
        ]);
    }

    /**
     * @Route("/trick/{slug}/{start}", name="moreComments", requirements={"start": "\d+"})
     * @param Trick $trick
     * @param int $start
     * @return Response
     */
    public function moreComments(Trick $trick, $start = 3)
    {
        return $this->render('trick/moreComments.html.twig', [
            'trick' => $trick,
            'start' => $start
        ]);
    }

    /**
     * Pour effacer un trick
     * 
     * @Route("/tricks/{slug}/delete", name="tricks_delete")
     * @Security("is_granted('ROLE_USER')", message="Vous ne pouvez pas supprimer ce Trick")
     * @return Response
     * 
     */
    public function delete(Trick $trick, TrickService $trickService)
    {
        $trickService->deleteTrick($trick);

        $this->addFlash(
            'success',
            "Le trick {$trick->getName()} a bien été supprimé"
        );

        return $this->redirectToRoute('tricks_index');
    }    
}
