<?php

// src/Form/LostitemType.php

namespace App\Form;

use App\Entity\Lostitem;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LostitemType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Lost Item Name',
                'attr' => ['class' => 'form-control'],
            ])
            ->add('description', TextType::class, [
                'label' => 'Details in the Description',
                'attr' => ['class' => 'form-control'],
            ])
            ->add('locationFound', TextType::class, [
                'label' => 'Location Lost',
                'attr' => ['class' => 'form-control'],
            ])
            ->add('dateFound', null, [
                'widget' => 'single_text',
                'label' => 'Date Lost',
            ])
            ->add('status', ChoiceType::class, [
                'choices' => [
                    'Lost' => 'Lost',
                ],
                'label' => 'Status',
            ])
            ->add('ownerName', TextType::class, [
                'label' => 'Owner Name',
                'attr' => ['class' => 'form-control'],
            ])
            ->add('ownerContact', EmailType::class, [
                'label' => 'Owner Email',
                'attr' => ['class' => 'form-control'],
            ])
            ->add('img', FileType::class, [
                'label' => 'Image',
                'required' => false,
                'data_class' => null,
                'attr' => [
                    'class' => 'form-control-file',
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Lostitem::class,
        ]);
    }
}
