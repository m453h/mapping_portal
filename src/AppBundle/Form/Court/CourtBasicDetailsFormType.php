<?php

namespace AppBundle\Form\Court;

use AppBundle\Entity\Court\Court;
use AppBundle\Form\DataTransformer\WardToNumberTransformer;
use AppBundle\Form\EventListener\AddSelectedDataCourtForm;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CourtBasicDetailsFormType extends  AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('courtLevel', EntityType::class, [
                'placeholder' => 'Choose the court level',
                'choice_label' => 'description',
                'required'=>true,
                'class' => 'AppBundle\Entity\Configuration\CourtLevel',
                'query_builder' => function(EntityRepository $repo) {
                    return $repo->createQueryBuilder('l')
                        ->orderBy('l.description','ASC');
                }
            ])
            ->add('courtName',null,['required'=>false]);

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
           'data_class' =>Court::class
        ]);
    }

}