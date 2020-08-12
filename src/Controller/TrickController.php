<?php

namespace App\Controller;


use App\Entity\Image;
use App\Entity\Trick;
use App\Entity\Video;
use App\Entity\Comment;
use App\Form\TrickType;
use App\Form\CommentType;
use App\Service\FileUploader;
use App\Service\ImageUploader;
use App\Repository\TrickRepository;
use Doctrine\ORM\EntityManagerInterface;
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
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @param ImageUploader $imageUploader
     * @param FileUploader $fileUploader
     * @return Response
     */
    public function create(Request $request, EntityManagerInterface $manager, ImageUploader $imageUploader, FileUploader $fileUploader)
    { 
        $trick = new Trick();
       
        $form = $this->createForm(TrickType::class, $trick);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isvalid()) {            
            
            $imageFile = $form->get('coverImage')->getData();

            if ($imageFile) {
            $imageFileName = $fileUploader->upload($imageFile);
            $trick->setCoverImage($imageFileName);
            }

            foreach ($trick->getImages() as $image) {
                $image->setTrick($trick);
                $image = $imageUploader->uploadImage($image);

                $manager->persist($image);
            }

            foreach ($trick->getVideos() as $video) {
                $video->setTrick($trick);
                $manager->persist($video);
                
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
     * @Security("is_granted('ROLE_USER') and user === trick.getUser()", message="Vous ne pouvez pas modifier ce Trick")
     * 
     * @param Trick $trick
     * @param Request $request
     * @return Response
     */
    public function edit(Trick $trick, Request $request, EntityManagerInterface $manager, ImageUploader $imageUploader, FileUploader $fileUploader ){
        
       
        $form = $this->createForm(TrickType::class, $trick);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isvalid()) {
            
            $imageFile = $form->get('coverImage')->getData();

            
            
            
            if ($imageFile) {
                $fileUploader->removeFile($trick->getCoverImage ());
                $imageFileName = $fileUploader->upload($imageFile);
                $trick->setCoverImage($imageFileName);
                }
                
                $images = $form->get('images')->getData();
                foreach ($images as $image) {                   
                       
                    $image->setTrick($trick);
                    $image = $imageUploader->uploadImage($image);
    
                    $manager->persist($image);
                
                }                     
                    
                foreach ($trick->getVideos() as $video) {
                    $video->setTrick($trick);
                    $manager->persist($video);
                    
                }
                
            $manager->persist($trick);                      
            $manager->flush();

            $this->addFlash(
                'success',
                "Le trick {$trick->getName()} a bien été modifié"
            );

            return $this->redirectToRoute('tricks_show', [
                'slug' => $trick->getSlug(),                
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
    public function show(Request $request,  Trick $trick, EntityManagerInterface $manager)
    {
        
        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        
        $form->handleRequest($request);
        
        if($form->isSubmitted() && $form->isValid()){

            $user= $this->getUser();
            $comment->setTrick($trick)
                    ->setUser($user);
            $manager->persist($comment);
            $manager->flush();

            $this->addFlash(
                'success',
                "Le commentaire a bien été enregistré"
            );

            return $this->redirectToRoute('tricks_show', [
                'slug' => $trick->getSlug()
            ]);
        }
        return $this->render('trick/show.html.twig', [
            'form' => $form->createView(),
            'trick' => $trick
        ]);
    }

    /**
     * Pour effacer un trick
     * 
     * @Route("/tricks/{slug}/delete", name="tricks_delete")
     * @Security("is_granted('ROLE_USER') and user === trick.getUser()", message="Vous ne pouvez pas supprimer ce Trick")
     * @return Response
     * 
     */
    public function delete(Trick $trick, EntityManagerInterface $manager, FileUploader $fileUploader){
        $manager->remove($trick);
        $manager->flush();

            $fileUploader->removeFile($trick->getCoverImage ());
            $images = $trick->getImages();
            foreach ($images as $image) {
                $fileUploader->removeFile($image->getPath());
               
            }
        $this->addFlash(
            'success',
            "Le trick {$trick->getName()} a bien été supprimé"
        );

        return $this->redirectToRoute('tricks_index');

    }

   /**
     * @Route("/tricks/delete/image/{id}", name="trick_delete_image")
     * @param Image $image
     * @param FileUploader $fileUploader
     * @return Response
     */
    public function deleteImage( Image $image , EntityManagerInterface $manager,  FileUploader $fileUploader)
    {
        
            $fileUploader->removeFile($image->getPath());
            $manager->remove($image);
            $manager->flush();
            
            $this->addFlash(
                'success',
                "Photo supprimée avec succès");
               
            return $this->redirectToRoute('tricks_index');        
    }


    /**
     * @Route("/tricks/delete/video/{id}", name="trick_delete_image")
     * @param Video $video
     * @return Response
     */
    public function deleteVideo( Video $video  , EntityManagerInterface $manager)
    {
        
             
            $manager->remove($video);
            $manager->flush();
            
            $this->addFlash(
                'success',
                "Vidéo supprimée avec succès");
               
            return $this->redirectToRoute('tricks_index');        
    }
 
}
