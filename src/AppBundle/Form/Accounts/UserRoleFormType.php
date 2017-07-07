<?php

namespace AppBundle\Form\Accounts;

use AppBundle\Entity\UserAccounts\UserRole;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


class UserRoleFormType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('role', EntityType::class, [
                'placeholder' => 'Choose a role',
                'choice_label' => 'roleName',
                'mapped' => true,
                'multiple'=>true,
                'expanded'=>true,
                'class' => 'AppBundle\Entity\UserAccounts\Role',
                'query_builder' => function(EntityRepository $repo) {
                    return $repo->createQueryBuilder('r')
                        ->orderBy('r.roleName','ASC');
                }
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class'=>UserRole::class
        ]);
    }


}