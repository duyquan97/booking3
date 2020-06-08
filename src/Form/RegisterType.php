<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class RegisterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', EmailType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'email not blank'
                    ]),
                ]
            ])
//            ->add(
//                $builder->create('role',TextType::class)
//                    ->addModelTransformer(new CallbackTransformer(
//                        function ($tagsAsArray) {
//                            return implode(', ', $tagsAsArray);
//                        },
//                        function ($tagsAsString) {
//                            return explode(', ', $tagsAsString);
//                        }
//                    ))
//            )
            ->add('password', TextType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'password not blank'
                    ]),
                    new Length([
                        'min' => 6,
                        'max' => 50,
                    ])
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'csrf_protection'   => false
        ]);
    }
}
