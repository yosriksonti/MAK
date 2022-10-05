<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\Location;
use App\Entity\Vehicule;
use App\Entity\Agence;
use Symfony\Component\Form\AbstractType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LocationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('Num')
            ->add('IP')
            ->add('Date_Res', DateType::class, [ 
                'widget' => 'single_text',
                ])
            ->add('Date_Loc', DateType::class, [ 
                'widget' => 'single_text',
                ])
            ->add('Date_Retour', DateType::class, [ 
                'widget' => 'single_text',
                ])
            ->add('Montant')
            ->add('Avance')
            ->add('Type')
            ->add('Etat')
            ->add('Status')
            ->add('User', EntityType::class, [
                'class' => User::class,
                'choice_label' => function($nom){
                    return $nom->getName();
                },
            ])
            ->add('Vehicule', EntityType::class, [
                'class' => Vehicule::class,
                'choice_label' => function($nom){
                    return $nom->getMarque();
                },
            ])
            ->add('Agence_Depart', EntityType::class, [
                'class' => Agence::class,
                'choice_label' => function($nom){
                    return $nom->getNom();
                },
            ])->add('Agence_Arrive', EntityType::class, [
                'class' => Agence::class,
                'choice_label' => function($nom){
                    return $nom->getNom();
                },
            ])
            ->add('isBabySeat')
            ->add('isPersonalDriver')
            ->add('isSecondDriver')
            ->add('isSTW')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Location::class,
        ]);
    }
}
