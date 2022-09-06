<?php

namespace App\Form;

use App\Entity\Vehicule;
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
            ->add('Description')
            ->add('Description_Det')
            ->add('Photo_Def')
            ->add('imageFile', VichImageType::class, [
                'required' => false,
            ])
            ->add('Photo_Saison')
        ;
    }

    public function configureOptions(OptionsResolver $optionsResolver): void
    {
        $optionsResolver->setDefaults([
            'data_class' => Vehicule::class,
        ]);
    }
}
