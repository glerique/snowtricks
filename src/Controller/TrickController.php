<?php

namespace App\Controller;

use App\Entity\Trick;
use App\Form\TrickType;
use App\Repository\TrickRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class TrickController extends AbstractController
{
    
     /**
     * Pour créer un trick
     *
     * @Route("/tricks/new", name="tricks_create")
     * 
     * 
     * @return Response
     */
    public function create(Request $request, EntityManagerInterface $manager, SluggerInterface $slugger)
    {
        $trick = new Trick();
        $form = $this->createForm(TrickType::class, $trick);
        $form->handleRequest($request);



        if ($form->isSubmitted() && $form->isvalid()) {
            $imageFile = $form->get('coverImage')->getData();

            // this condition is needed because the 'brochure' field is not required
            // so the PDF file must be processed only when a file is uploaded
            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$imageFile->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    $imageFile->move(
                        $this->getParameter('images_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

                // updates the 'brochureFilename' property to store the PDF file name
                // instead of its contents
                $trick->setCoverImage($newFilename);
            }

            $manager->persist($trick);
            $manager->flush();

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
     * 
     * @return Response
     */
    public function edit(Trick $trick, Request $request, EntityManagerInterface $manager, SluggerInterface $slugger){
        
       
        $form = $this->createForm(TrickType::class, $trick);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isvalid()) {
            
            $imageFile = $form->get('coverImage')->getData();

            // this condition is needed because the 'brochure' field is not required
            // so the PDF file must be processed only when a file is uploaded
            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$imageFile->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    $imageFile->move(
                        $this->getParameter('images_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

                // updates the 'brochureFilename' property to store the PDF file name
                // instead of its contents
                $trick->setCoverImage($newFilename);
            }
            
            
            $manager->persist($trick);                      
            $manager->flush();

            $this->addFlash(
                'success',
                "Le trick {$trick->getName()} a bien été modifié"
            );

            return $this->redirectToRoute('tricks_show', [
                'slug' => $trick->getSlug()
            ]);
        }

        return $this->render('trick/edit.html.twig',[
            'form' => $form->createView(),
            'trick' => $trick
        ]);
    }
    
    
    /**
     * Pour afficher plusieurs tricks
     * 
     * @Route("/", name="tricks_index")
     *      * 
     */
    public function index(TrickRepository $repository)
    {

        //$repository = $this->getDoctrine()->getRepository(Trick::class);
        $tricks = $repository->findAll();
        return $this->render('trick/index.html.twig', [
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
    public function show(Trick $trick)
    {
        return $this->render('trick/show.html.twig', [
            'trick' => $trick
        ]);
    }

    /**
     * Pour effacer un trick
     * 
     * @Route("/tricks/{slug}/delete", name="tricks_delete")
     * @return Response
     * 
     */
    public function delete(Trick $trick, EntityManagerInterface $manager){
        $manager->remove($trick);
        $manager->flush();

        $this->addFlash(
            'success',
            "Le trick {$trick->getName()} a bien été supprimé"
        );

        return $this->redirectToRoute('tricks_index');

    }
}
