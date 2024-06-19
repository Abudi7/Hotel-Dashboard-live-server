<?php

namespace App\Form;
use App\Entity\Booking;
use App\Entity\Rooms; 
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType; 
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security; 
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;

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
           // Add other form fields as needed
           ->add('startdate', DateType::class, [
                'widget' => 'single_text',
                'constraints' => [
                    new Callback([$this, 'validateStartDate']),
                ],
            ])
            ->add('enddate', DateType::class, [
                'widget' => 'single_text',
                'constraints' => [
                    new GreaterThanOrEqual([
                        'value' => 'today',
                        'message' => 'End date must be after the start date.',
                    ]),
                ],
            ])
            ->add('customername', TextType::class, [
                'data' => $user->getUserIdentifier(), // Set the default value to the email of the logged-in user
                'disabled' => true, // Make the field disabled so it cannot be edited
            ])
            ->add('address', AddressType::class, [
                'label' => 'Billing Address',
            ])
            ->add('submit', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            // Configure your data class here
            'data_class' => Booking::class,
        ]);
    }

    public function validateStartDate($value, ExecutionContextInterface $context)
    {
        $formData = $context->getRoot()->getData();
        $endDate = $formData->getEnddate();
        
        if ($endDate !== null && $value >= $endDate) {
            // Check if start date is the same or after end date
            $context->buildViolation('The start date must be at least one night before the end date.')
                ->atPath('startdate')
                ->addViolation();
        } else if ($endDate !== null) {
            // Check if there is at least one night between start and end date
            $interval = $value->diff($endDate);
            if ($interval->days < 1) {
                $context->buildViolation('There must be at least one night between the start and end dates.')
                    ->atPath('startdate')
                    ->addViolation();
            }
        }
    
        if ($value < new \DateTime('today')) {
            $context->buildViolation('Start date cannot be in the past.')
                ->atPath('startdate')
                ->addViolation();
        }
    }
    
}
