<?php

namespace App\Form;

use App\Entity\Client;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ClientType extends AbstractType
{
    public function buildForm(FormBuilderInterface $formBuilder, array $options): void
    {
        $formBuilder
            ->add('Nom')
            ->add('Prenom')
            ->add('Pays')
            ->add('Telephone')
            ->add('Add1')
            ->add('Add2')
            ->add('Permis')
            ->add('Date_Permis')
            ->add('CIN')
            ->add('Date_CIN')
            ->add('Date_Naissance')
            //->add('User')
        ;
    }

    public function configureOptions(OptionsResolver $optionsResolver): void
    {
        $optionsResolver->setDefaults([
            'data_class' => Client::class,
        ]);
    }
}
