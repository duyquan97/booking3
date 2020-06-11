<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;

use FOS\UserBundle\Form\Type\RegistrationFormType as BaseRegistrationFormType;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('roles', ChoiceType::class, [
                'choices' => [
                    'Admin' => 'ROLE_ADMIN',
                    'USER' => 'ROLE_USER',
                    'SUPER ADMIN' => 'ROLE_SUPER_ADMIN',
                ],
                'multiple' => true,

            ])
        ;
    }

    public function getParent()
    {
       return BaseRegistrationFormType::class;
    }
}
