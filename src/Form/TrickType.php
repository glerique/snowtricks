<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\Trick;
use App\Form\ImageType;
use App\Form\VideoType;
use App\Entity\Category;
use App\Form\ApplicationType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class TrickType extends ApplicationType
{



    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, $this->getConfiguration('Nom', 'Nom'))
            ->add('description', TextareaType::class, $this->getConfiguration('Description', 'Description'))
            ->add('coverImage', FileType::class,  [
                'label' => 'Image de couverture',
                'mapped' => false,
                'attr' => [
                    'placeholder' => 'Image de couverture'
                ],
                'required' => false
            ])
            ->add('category', EntityType::class, $this->getEntityConfiguration(Category::class, 'Categorie', 'name'))
            ->add('user', EntityType::class, $this->getEntityConfiguration(User::class, 'Utilisateur', 'firstname'))
            ->add(
                'images',
                CollectionType::class,
                [
                    'entry_type' => ImageType::class,
                    'allow_add' => true,
                    'allow_delete' => true
                ]
            )
            ->add(
                'videos', 
                CollectionType::class,
                [
                    'entry_type' => VideoType::class,
                    'allow_add' => true,
                    'allow_delete' => true
                ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Trick::class,
        ]);
    }
}
