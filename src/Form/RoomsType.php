<?php

namespace App\Form;

use App\Entity\Rooms;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class RoomsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name',TextType::class,[
                'required' => true,
                'constraints' => [
                    new NotBlank([
                        'message' => 'fromDate not blank'
                    ]),
                ]
            ])
            ->add('short_description')
            ->add('description')
            ->add('province',null,[
                'required' => true,
                'constraints' => [
                    new NotBlank([
                        'message' => 'fromDate not blank'
                    ]),
                ]
            ])
            ->add('district',null,[
                'required' => true,
                'constraints' => [
                    new NotBlank([
                        'message' => 'fromDate not blank'
                    ]),
                ]
            ])
            ->add('street',null,[
                'required' => true,
                'constraints' => [
                    new NotBlank([
                        'message' => 'fromDate not blank'
                    ]),
                ]
            ])
            ->add('person',NumberType::class,[
                'required' => true,
                'constraints' => [
                    new NotBlank([
                        'message' => 'fromDate not blank'
                    ]),
                ]
            ])
            ->add('type',NumberType::class)
            ->add('status',NumberType::class)
            ->add('featured',NumberType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Rooms::class,
        ]);
    }
}
