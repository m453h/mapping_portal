<?php


namespace AppBundle\Form\Configuration;

use AppBundle\Entity\Configuration\Contact;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;

class ContactFormType extends  AbstractType
{


    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('name',null,['required'=>true, 'mapped'=>true,
                'constraints'=>[
                    new NotBlank(['message'=>'This field can not be blank']),
                ]])
            ->add('road',null,['required'=>true, 'mapped'=>true,
                'constraints'=>[
                    new NotBlank(['message'=>'This field can not be blank']),
                ]])
            ->add('address',null,['required'=>true, 'mapped'=>true,
                'constraints'=>[
                    new NotBlank(['message'=>'This field can not be blank']),
                ]])
            ->add('fax',null,['required'=>true,'mapped'=>true,
                'constraints'=>[
                    new NotBlank(['message'=>'This field can not be blank']),
                ]
            ])
            ->add('phone',null,['required'=>false,
                'constraints'=>[
                    new NotBlank(['message'=>'This field can not be blank']),
                ]
                ])
            ->add('email',TextType::class,['required'=>false,
                'constraints'=>[
                    new NotBlank(['message'=>'This field can not be blank']),
                    new Email([
                        'message'=> "The email '{{ value }}' is not a valid email.",
                        'checkMX'=>true
                    ])
                ]]);


    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' =>Contact::class
        ]);
    }

}