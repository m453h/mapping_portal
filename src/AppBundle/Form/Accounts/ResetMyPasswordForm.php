<?php

namespace AppBundle\Form\Accounts;

use AppBundle\Entity\UserAccounts\User;
use Bafford\PasswordStrengthBundle\Validator\Constraints\PasswordStrength;
use Bafford\PasswordStrengthBundle\Validator\Constraints\PasswordStrengthValidator;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class ResetMyPasswordForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('currentPassword',PasswordType::class,
                [
                    'mapped'=>false,
                    'constraints'=>[
                     new NotBlank(['message'=>'You must provide your current password'])]
                ])
            ->add('plainPassword', RepeatedType::class, array(
                'type' => PasswordType::class,
                'invalid_message' => 'The password fields must match.',
                'options' => array('attr' => array('class' => 'password-field')),
                'required' => true,
                'mapped'=>false,
                'first_options'  => array('label' => 'Password'),
                'second_options' => array('label' => 'Repeated Password'),
                'constraints'=>[
                    new NotBlank(['message'=>'Password can not be empty']),
                    new PasswordStrength(['minLength'=>8,'requireNumbers'=>true])
                ]
        ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' =>User::class
        ]);
    }

}
