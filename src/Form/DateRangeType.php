<?php
// src/Form/DateRangeType.php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;

class DateRangeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $today = new \DateTime();
        $todayString = $today->format('Y-m-d');

        $builder
            ->add('startdate', DateType::class, [
                'widget' => 'single_text',
                'label' => 'CHECK-IN',
                'constraints' => [
                    new NotBlank(['message' => 'Start date cannot be blank.']),
                    new GreaterThanOrEqual([
                        'value' => $today,
                        'message' => 'Start date cannot be in the past.'
                    ]),
                ],
                'attr' => [
                    'min' => $todayString,
                    'class' => 'form-control',
                    'placeholder' => 'Select start date',
                    'autocomplete' => 'off',
                ],
            ])
            ->add('enddate', DateType::class, [
                'widget' => 'single_text',
                'label' => 'CHECK-OUT',
                'constraints' => [
                    new NotBlank(['message' => 'End date cannot be blank.']),
                    new GreaterThanOrEqual([
                        'value' => $today,
                        'message' => 'End date cannot be in the past.'
                    ]),
                    new Callback([$this, 'validateDates']),
                ],
                'attr' => [
                    'min' => $todayString,
                    'class' => 'form-control',
                    'placeholder' => 'Select end date',
                    'autocomplete' => 'off',
                ],
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Check Availability',
                'attr' => [
                    'class' => 'btn btn-primary',
                ],
            ]);
    }

    public function validateDates($endDate, ExecutionContextInterface $context): void
    {
        $form = $context->getRoot();
        $startDate = $form['startdate']->getData();

        if ($startDate && $endDate && $startDate > $endDate) {
            $context->buildViolation('End date cannot be earlier than start date.')
                ->atPath('enddate')
                ->addViolation();
        }
    }
}

