<?php

namespace App\Form;

use App\Entity\Booking;
use App\Form\DataTransformer\GuestTransformer;
use App\Form\DataTransformer\RoomTransformer;
use App\Form\EventListener\BookingListener;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class BookingType extends AbstractType
{
    private $roomTransformer;
    private $guestTransformer;
    public function __construct(RoomTransformer $roomTransformer, GuestTransformer $guestTransformer)
    {
        $this->guestTransformer = $guestTransformer;
        $this->roomTransformer = $roomTransformer;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('amount', NumberType::class,[
                'constraints' => [
                    new NotBlank([
                        'message' => 'amount not blank'
                    ]),
                ]
            ])
            ->add(
                $builder->create('guest',TextType::class,[
                    'invalid_message' => 'That is not a valid room number',
                    'required' => true,
                    'constraints' => [
                        new NotBlank([
                            'message' => 'guest not blank'
                        ]),
                    ]])
                    ->addModelTransformer($this->guestTransformer))
            ->add('fromDate', DateType::class,[
                'required' => true,
                'widget' => 'single_text',
                'html5' => false,
                'constraints' => [
                    new NotBlank([
                        'message' => 'fromDate not blank'
                    ]),
                ]
            ])
            ->add('toDate',DateType::class,[
                'required' => true,
                'widget' => 'single_text',
                'html5' => false,
                'constraints' => [
                    new NotBlank([
                        'message' => 'toDate not blank'
                    ]),
                ]
            ])
            ->add(
                $builder->create('room',TextType::class,[
                    'invalid_message' => 'That is not a valid room number',
                    'required' => true,
                    'constraints' => [
                        new NotBlank([
                            'message' => 'room not blank'
                        ]),
                    ]])
                    ->addModelTransformer($this->roomTransformer))
            ->addEventSubscriber(new BookingListener())
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Booking::class,
        ]);
    }
}
