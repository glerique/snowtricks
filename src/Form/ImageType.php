<?php

namespace App\Form;

use App\Entity\Image;
use App\Form\ApplicationType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;


class ImageType extends ApplicationType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
                 
        $builder
        ->add('file',
            FileType::class,

                [
                    'required' => false,                    
                    'attr' => [
                        'placeholder' => 'Image de trick'                    
                    ]
                        ]) 
        ->add('caption', TextType::class, $this->getConfiguration("Légende", "Légende de l'image"))                ; 

        
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Image::class,
        ]);
    }
}
