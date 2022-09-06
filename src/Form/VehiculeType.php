<?php

namespace App\Form;

use App\Entity\Park;
use App\Entity\Vehicule;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichImageType;

class VehiculeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $formBuilder, array $options): void
    {
        $formBuilder
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
            ->add('Park', EntityType::class, [
                'class' => Park::class,
                'choice_label' => function($nom){
                    return $nom->getNom();
                },
            ])
            ->add('Description')
            ->add('Description_Det')
            ->add('Def', FileType::class, [
                'required' => false,
            ])
            ->add('Reel', VichImageType::class, [
                'required' => false,
            ])
            ->add('Saison', VichImageType::class, [
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $optionsResolver): void
    {
        $optionsResolver->setDefaults([
            'data_class' => Vehicule::class,
        ]);
    }
}
