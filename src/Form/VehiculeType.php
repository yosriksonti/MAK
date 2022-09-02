<?php

namespace App\Form;

use App\Entity\Vehicule;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class VehiculeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('Marque')
            ->add('Modele')
            ->add('Categorie')
            ->add('Boite')
            ->add('Carb')
            ->add('Nb_Places')
            ->add('Nb_Portes')
            ->add('Nb_Val')
            ->add('Caut')
            ->add('Clim')
            ->add('Description')
            ->add('Description_Det')
            ->add('Photo_Def')
            ->add('Photo_reel')
            ->add('Photo_Saison')
            ->add('Park')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Vehicule::class,
        ]);
    }
}
