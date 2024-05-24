<?php

namespace App\Form;

use App\Entity\Booking;
use App\Entity\Rooms;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType; // Import TextType
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security; // Import Security

class BookingType extends AbstractType
{
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $user = $this->security->getUser(); // Get the logged-in user

        $builder
            ->add('startdate', null, [
                'widget' => 'single_text',
            ])
            ->add('enddate', null, [
                'widget' => 'single_text',
            ])
            ->add('customername', TextType::class, [
                'data' => $user->getUserIdentifier(), // Set the default value to the name of the logged-in user
                'disabled' => true, // Make the field disabled so it cannot be edited
            ])
            ->add('address', AddressType::class, [
                'label' => 'Billing Address'
            ])
            ->add('rooms', EntityType::class, [
                'class' => Rooms::class,
                'choice_label' => 'name', // Assuming 'name' is a property of the Room entity
                'label' => 'Room',
                // Add any additional options if needed
            ])
            ->add('submit', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Booking::class,
        ]);
    }
}
