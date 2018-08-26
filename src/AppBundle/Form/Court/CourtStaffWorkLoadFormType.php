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

class CourtStaffWorkLoadFormType extends  AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('casesPerYear',null,['required'=>false])
            ->add('populationServed',null,['required'=>false])
            ->add('numberOfJustices',null,['required'=>false])
            ->add('numberOfJudges',null,['required'=>false])
            ->add('numberOfResidentMagistrates',null,['required'=>false])
            ->add('numberOfDistrictMagistrates',null,['required'=>false])
            ->add('numberOfMagistrates',null,['required'=>false])
            ->add('numberOfCourtClerks',null,['required'=>false])
            ->add('numberOfNonJudiciaryStaff',null,['required'=>false]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
           'data_class' =>Court::class
        ]);
    }

}