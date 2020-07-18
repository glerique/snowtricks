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
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\String\Slugger\AsciiSlugger;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {

        $admin = new Role;
        $admin->setName("ROLE_ADMIN");

        $manager->persist($admin);

        $role = new Role;
        $role->setName("ROLE_USER");

        $manager->persist($role);

        $adminUser = new User();
        $adminUser->setLastName("lerique")
            ->setfirstName("gael")
            ->setEmail("gael.maing59@gmail.com")
            ->setNickname("glerique")
            ->setPassword("admin")
            ->setRoles($admin)
            ->setAvatar("avatarAdmin");

        $manager->persist($adminUser);


        $users = [];
        for ($i = 1; $i < 11; $i++) {
            $user = new User();
            $user->setLastName("nom$i")
                ->setfirstName("prenom$i")
                ->setEmail("mail$i@gmail.com")
                ->setNickname("nickname$i")
                ->setPassword("mdp$i")
                ->setRoles($role)
                ->setAvatar("avatar$i");

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
                ->setDescription("content$i")
                ->setCreatedAt(new \DateTime('now'))
                ->setSlug($slug)
                ->setCoverImage("uploads/images/720.jpg")
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
            $video->setUrl("url$i")
                ->setCaption("caption$i")
                ->setTrick($trick);

            $manager->persist($video);
        }

        $manager->flush();
    }
}
