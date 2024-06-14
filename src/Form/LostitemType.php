<?php

// src/Form/LostitemType.php

namespace App\Form;

use App\Entity\Lostitem;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\HttpFoundation\File\File;

class LostitemType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('description')
            ->add('locationFound', TextType::class, [
                'label' => 'Location Found',
                'attr' => ['class' => 'form-control'],
            ])
            ->add('dateFound', null, [
                'widget' => 'single_text',
            ])
            ->add('status', ChoiceType::class, [
                'choices' => [
                    'Lost' => 'Lost',
                    'Found' => 'Found',
                    'Return' => 'Return',
                ],
            ])
            ->add('ownerName')
            ->add('ownerContact')
            ->add('img', FileType::class, [
                'label' => 'Image',
                'required' => false,
                'data_class' => null, // Important: Add this line
                'attr' => [
                    'class' => 'form-control-file',
                ],
            ]);

        // // Add a data transformer to handle the 'img' field
        // $builder->get('img')->addModelTransformer(new CallbackTransformer(
        //     function ($image) {
        //         // Transform the filename (string) to a File object
        //         return $image ? new File($this->getParameter('lostFounds_directory') . '/' . $image) : null;
        //     },
        //     function ($file) {
        //         // Transform the File object back to a string (filename)
        //         return $file instanceof File ? $file->getFilename() : null;
        //     }
        // ));
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Lostitem::class,
        ]);
    }
}

