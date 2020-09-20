<?php

namespace App\Form;

use App\Entity\User;
use App\Form\ApplicationType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class RegistrationType extends ApplicationType
{


    

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstName', TextType::class, $this->getConfiguration("Prénom","Prénom"))
            ->add('lastName', TextType::class, $this->getConfiguration("Nom","Nom"))
            ->add('nickname', TextType::class, $this->getConfiguration("Pseudo","Pseudo"))
            ->add('email', EmailType::class, $this->getConfiguration("Email","Email"))
            ->add('password', PasswordType::class, $this->getConfiguration("Mot de passe","Mot de passe"))
            ->add('confirmPassword', PasswordType::class, $this->getConfiguration("Confirmation du mot de passe","Confirmation du mot de passe"))
            ->add('avatar', FileType::class,  [
                'label' => 'Avatar',
                'mapped' => false,                
                'attr' => [
                    'placeholder' => 'Avatar'
                ],
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                        ],
                        'mimeTypesMessage' => 'Veuillez uploader une image jpeg ou png',                        
                    ])
                ] 
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}