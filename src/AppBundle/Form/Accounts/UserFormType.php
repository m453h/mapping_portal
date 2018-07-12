<?php


namespace AppBundle\Form\Accounts;


use AppBundle\Entity\UserAccounts\Role;
use AppBundle\Entity\UserAccounts\User;
use AppBundle\Entity\UserAccounts\UserRole;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserFormType extends  AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('givenNames',null,['required'=>true,'mapped'=>true])
            ->add('surname',null,['required'=>true,'mapped'=>true])
            ->add('mobilePhone',null,['required'=>false])
            ->add('email',TextType::class,['required'=>false]);

       

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' =>User::class
        ]);
    }

}