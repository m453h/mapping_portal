<?php


namespace AppBundle\Form\AppUsers;


use AppBundle\Entity\AppUsers\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AppUserFormType extends  AbstractType
{
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstName',null,['required'=>true,'mapped'=>true])
            ->add('surname',null,['required'=>true,'mapped'=>true])
            ->add('mobile',null,['required'=>true,'mapped'=>true])
            ->add('username',null,['required'=>true,'mapped'=>true]);

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {

                  if(!$event->getData() instanceof User)
                  {
                      $form = $event->getForm();

                      $form->add('password', RepeatedType::class, array(
                          'type' => PasswordType::class,
                          'invalid_message' => 'The password fields must match.',
                          'options' => array('attr' => array('class' => 'password-field')),
                          'required' => true,
                          'first_options'  => array('label' => 'Password'),
                          'second_options' => array('label' => 'Repeated Password'),
                      ))
                          ->add('username',null,['required'=>true,'mapped'=>true]);


                  }
        });



    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' =>User::class
        ]);
    }

}