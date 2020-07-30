<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;



class ApplicationType extends AbstractType {


    /**
     * Configuration des champs du formulaire
     * @param string $label
     * @param string $placeholder
     * @param array $options
     * @return array 
     */

    protected function getConfiguration($label, $placeholder, $options = [])
    {
        return array_merge([
            'label' => $label,
            'attr' => [
                'placeholder' => $placeholder
            ]
            ] ,$options
        );
    }

    protected function getEntityConfiguration($class, $label, $choiceLabel)
    {
        return [
            'class' => $class,
            'label' => $label,
            'choice_label' => $choiceLabel
        ];
    }

}