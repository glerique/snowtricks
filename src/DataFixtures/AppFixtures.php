<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Image;
use App\Entity\Trick;
use App\Entity\Video;
use App\Entity\Comment;
use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use \Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\String\Slugger\AsciiSlugger;

class AppFixtures extends Fixture
{
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {

        $users = [];
        for ($i = 1; $i < 11; $i++) {
            $user = new User();
            $password = $this->encoder->encodePassword($user, 'password');

            $user->setLastName("Nom$i")
                ->setfirstName("Prenom$i")
                ->setEmail("user$i@snowtricks.com")
                ->setNickname("Nickname$i")
                ->setPassword("$password")
                ->setAvatar("avatar.png")
                ->setToken(md5(bin2hex(openssl_random_pseudo_bytes(6))))
                ->setValidated(1);

            $manager->persist($user);
            $users[] = $user;
        }


        $categories = [];
        for ($i = 1; $i < 11; $i++) {
            $categorie = new Category();
            $categorie->setname("Categorie$i");

            $manager->persist($categorie);
            $categories[] = $categorie;
        }



        $tricks = [];

        for ($i = 1; $i < 11; $i++) {
            $category = $categories[mt_rand(0, count($categories) - 1)];
            $user = $users[mt_rand(0, count($users) - 1)];
            $name = "Trick$i";
            $slugger = new AsciiSlugger();
            $slug =  $slugger->slug($name);
            $description = "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Phasellus dignissim placerat tellus vel feugiat. Integer at congue eros. Integer et sem non enim malesuada dignissim. Proin tincidunt id elit a dapibus. Donec volutpat porttitor tellus nec varius. Sed accumsan pellentesque magna, dictum accumsan velit laoreet vel. Sed iaculis pretium mollis. Orci varius natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Aliquam erat volutpat. Nulla consequat fermentum magna eget pellentesque. Nunc felis orci, bibendum ut semper ac, sollicitudin sed arcu. Nulla tincidunt lectus a turpis dignissim, sed placerat turpis fermentum. Nam ultrices viverra urna at lobortis. Curabitur vitae dictum augue. Integer cursus, risus facilisis ultrices maximus, sem tellus molestie nibh, a ornare magna sapien tristique tortor. Nulla tempor felis nunc, quis iaculis erat pellentesque rutrum.

            Mauris pretium erat a magna sagittis tempus. Duis eu orci vitae nisl aliquam cursus. Nunc hendrerit diam nec mi ultrices consectetur. Ut nec tortor ac sapien pharetra pharetra. Ut et neque ac augue condimentum rhoncus. In commodo magna interdum tortor iaculis, quis gravida justo tristique. Donec ipsum risus, elementum ut dolor eget, eleifend euismod ante. Duis eu ligula turpis. Donec gravida metus a purus pulvinar interdum.";
            $trick = new Trick();
            $trick->setName($name)
                ->setDescription("$description")
                ->setCreatedAt(new \DateTime('now'))
                ->setSlug($slug)
                ->setCoverImage("$i.jpg")
                ->setCategory($category)
                ->setUser($user);

            $manager->persist($trick);
            $tricks[] = $trick;
        }

       

        for ($i = 1; $i < 11; $i++) {
            $trick = $tricks[mt_rand(0, count($tricks) - 1)];
            $user = $users[mt_rand(0, count($users) - 1)];
            $comment = new Comment();
            $comment->setContent("comment$i")
                ->setCreatedAt(new \DateTime('now'))
                ->setUser($user)
                ->setTrick($trick);

            $manager->persist($comment);
        }
        
        for ($i = 1; $i < 11; $i++) {
            $trick = $tricks[mt_rand(0, count($tricks) - 1)];
            $image = new Image();
            $image->setPath("i$i.jpg")
                ->setCaption("caption$i")
                ->setTrick($trick);

            $manager->persist($image);
        }
        
        for ($i = 1; $i < 11; $i++) {
            $trick = $tricks[mt_rand(0, count($tricks) - 1)];           
            $video = new Video();
            $video->setUrl("https://www.youtube.com/embed/Hgv44f3ygOk")
                ->setCaption("Le gende de la vidéo $i")
                ->setTrick($trick);

            $manager->persist($video);
        }
        
           
        $manager->flush();
    }
}
