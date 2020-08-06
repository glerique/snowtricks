<?php

namespace App\DataFixtures;

use App\Entity\Role;
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
    
    public function __construct(UserPasswordEncoderInterface $encoder){
        $this->encoder = $encoder;


    }

    public function load( ObjectManager $manager)
    {

        $adminRole = new Role;
        $adminRole->setName("ROLE_ADMIN");
        $manager->persist($adminRole);
        

        $user = new User();
        $password = $this->encoder->encodePassword($user, 'password');
        $user->setLastName("lerique")
            ->setfirstName("gael")
            ->setEmail("gael.maing59@gmail.com")
            ->setNickname("glerique")
            ->setPassword($password)            
            ->setAvatar("avatar.png")
            ->setToken(md5(bin2hex(openssl_random_pseudo_bytes(6))))
            ->addUserRole($adminRole);
            

        $manager->persist($user);

        
        $users = [];
        for ($i = 1; $i < 11; $i++) {
            $user = new User();

            
            $user->setLastName("nom$i")
                ->setfirstName("prenom$i")
                ->setEmail("mail$i@gmail.com")
                ->setNickname("nickname$i")
                ->setPassword("$password")                
                ->setAvatar("avatar.png")
                ->setToken(md5(bin2hex(openssl_random_pseudo_bytes(6))));

            $manager->persist($user);
            $users[] = $user;
        }


        $categories = [];
        for ($i = 1; $i < 11; $i++) {
            $categorie = new Category();
            $categorie->setname("cat$i");

            $manager->persist($categorie);
            $categories[] = $categorie;
        }



        $tricks = [];

        for ($i = 1; $i < 11; $i++) {
            $category = $categories[mt_rand(0, count($categories) - 1)];
            $user = $users[mt_rand(0, count($users) - 1)];
            $name = "name$i";
            $slugger = new AsciiSlugger();
            $slug =  $slugger->slug($name);
            $trick = new Trick();
            $trick->setName($name)
                ->setDescription("description$i")
                ->setCreatedAt(new \DateTime('now'))
                ->setSlug($slug)
                ->setCoverImage("720.jpg")
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
            $image->setPath("path$i")
                ->setCaption("caption$i")
                ->setTrick($trick);

            $manager->persist($image);
        }

        for ($i = 1; $i < 11; $i++) {
            $trick = $tricks[mt_rand(0, count($tricks) - 1)];
            $video = new Video();
            $video->setUrl("1080.jpg")
                ->setCaption("caption$i")
                ->setTrick($trick);

            $manager->persist($video);
        }

        $manager->flush();
    }
}
