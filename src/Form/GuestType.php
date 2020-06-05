<?php

namespace App\Form;

use App\Entity\Guests;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class GuestType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name',TextType::class,[
                'constraints' => [
                    new NotBlank([
                        'message' => 'phone not blank!'
                    ]),
                ]
            ])
            ->add('phone',TextType::class,[
                'constraints' => [
                    new NotBlank([
                        'message' => 'phone not blank!'
                    ]),
                    new Length([
                        'min' => 10,
                        'max' => 12,
                    ])
                ]
            ])
            ->add('email', EmailType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'phone not blank!'
                    ]),
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Guests::class,
            'csrf_protection'   => false
        ]);
    }
}
