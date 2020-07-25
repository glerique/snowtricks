<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\Trick;
use App\Entity\Category;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class TrickType extends AbstractType
{
    /**
     * Configuration des champs du formulaire
     * @param string $label
     * @param string $placeholder
     * @param array $options
     * @return array 
     */

    public function getConfiguration($label, $placeholder, $options = [])
    {
        return array_merge([
            'label' => $label,
            'attr' => [
                'placeholder' => $placeholder
            ]
            ] ,$options
        );
    }

    public function getEntityConfiguration($class, $label, $choiceLabel)
    {
        return [
            'class' => $class,
            'label' => $label,
            'choice_label' => $choiceLabel
        ];
    }


    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, $this->getConfiguration('Nom', 'Nom'))            
            ->add('description', TextareaType::class, $this->getConfiguration('Description', 'Description'))
            ->add('coverImage', FileType::class,  [
                'label' => 'Image de couverture',
                'mapped' => false, 
                'required' => false
            ])
            ->add('category', EntityType::class, $this->getEntityConfiguration(Category::class, 'Categorie', 'name'))
            ->add('user', EntityType::class, $this->getEntityConfiguration(User::class, 'Utilisateur', 'firstname'));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Trick::class,
        ]);
    }
}
