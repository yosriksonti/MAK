<?php

namespace App\Form;

use App\Entity\Location;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LocationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('Num')
            ->add('IP')
            ->add('Date_Res')
            ->add('Date_Loc')
            ->add('Date_Retour')
            ->add('Montant')
            ->add('Avance')
            ->add('Type')
            ->add('Etat')
            ->add('Status')
            ->add('Client')
            ->add('Vehicule')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Location::class,
        ]);
    }
}
