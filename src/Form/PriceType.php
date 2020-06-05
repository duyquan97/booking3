<?php

namespace App\Form;

use App\Entity\Prices;
use App\Form\DataTransformer\RoomTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Date;
use Symfony\Component\Validator\Constraints\NotBlank;

class PriceType extends AbstractType
{
    private $roomTransformer;
    public function __construct(RoomTransformer $roomTransformer)
    {
        $this->roomTransformer = $roomTransformer;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('price',TextType::class,[
                'required' => true,
                'constraints' => [
                    new NotBlank([
                        'message' => 'price not blank'
                    ]),
                ]
            ])
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
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Prices::class,
            'csrf_protection'   => false
        ]);
    }
}
