<?php


namespace AppBundle\Form\Accounts;


use AppBundle\Entity\UserAccounts\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserFormType extends  AbstractType
{

    /**
     * @var RequestStack
     */
    private $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }


    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('username',null,['required'=>true,'mapped'=>true])
        ->add('givenNames',null,['required'=>true,'mapped'=>true])
        ->add('surname',null,['required'=>true,'mapped'=>true])
        ->add('mobilePhone',null,['required'=>false])
        ->add('email',null,['required'=>false])
        ->add('staff',StaffFormType::class);
        
       

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' =>User::class
        ]);
    }

}