<?php

namespace App\Form;

use App\Entity\Booking;
use App\Entity\Rooms; // Import Rooms entity
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType; // Import TextType
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security; // Import Security
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Context\ExecutionContextInterface;


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
            ->add('startdate', DateType::class, [
                'widget' => 'single_text',
            ])
            ->add('enddate', DateType::class, [
                'widget' => 'single_text',
            ])
            ->add('customername', TextType::class, [
                'data' => $user->getUserIdentifier(), // Set the default value to the email of the logged-in user
                'disabled' => true, // Make the field disabled so it cannot be edited
            ])
            ->add('address', AddressType::class, [
                'label' => 'Billing Address'
            ])
            // ->add('rooms', TextType::class)
            ->add('submit', SubmitType::class)
        ;
    }

    
    public function validateDates($object, ExecutionContextInterface $context): void
    {
        $startDate = $context->getRoot()->get('startdate')->getData();
        $endDate = $context->getRoot()->get('enddate')->getData();

        if ($startDate && $endDate && $startDate > $endDate) {
            $context->buildViolation('End date must be after start date.')
                ->atPath('enddate')
                ->addViolation();
        }
    }
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Booking::class,
            'constraints' => [
                new Callback([$this, 'validateDates']),
            ],
        ]);
    }
}
