<?php

namespace App\Form;

use App\Entity\Rooms;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RoomsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', null, [
                'attr' => [
                    'placeholder' => 'Enter the room name',
                    'class' => 'form-control' // Add any additional classes you need
                ]
            ])
            ->add('description', null, [
                'attr' => [
                    'placeholder' => 'Enter the room description',
                    'class' => 'form-control' // Add any additional classes you need
                ]
            ])
            ->add('price', MoneyType::class, [
                'currency' => 'EUR',
                'scale' => 2, // Adjust the scale as per your requirements
            ])
            // ->add('createdat', null, [
            //     'widget' => 'single_text',
            // ])
            ->add('type', ChoiceType::class, [
                'choices' => [
                    '3 Personen' => '3 Personen',
                    '1 Person' => '1 Person',
                    '4 Personen' => '4 Personen',
                    '10 Personen' => '10 Personen',
                ],
            ])
            ->add('img',FileType::class, [
                'mapped' => false,
                'required' => false,

            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Rooms::class,
        ]);
    }
}
