<?php

namespace App\Form;

use App\Entity\Payment;
use App\Entity\User;
use App\Entity\Location;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PaymentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('sessionId')
            ->add('status')
            ->add('total')
            ->add('createdOn',DateType::class, [ 
                'widget' => 'single_text',
                ])
            ->add('User', EntityType::class, [
                'class' => User::class,
                'choice_label' => function($cin){
                    return $cin->getCIN();
                },
            ])
            ->add('Location', EntityType::class, [
                'class' => Location::class,
                'choice_label' => function($num){
                    return $num->getNum();
                },
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Payment::class,
        ]);
    }
}
