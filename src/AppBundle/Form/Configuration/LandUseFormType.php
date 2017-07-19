<?php

namespace AppBundle\Form\Configuration;

use AppBundle\Entity\Configuration\CourtCategory;
use AppBundle\Entity\Configuration\EconomicActivity;
use AppBundle\Entity\Configuration\LandUse;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LandUseFormType extends  AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('description',null,['required'=>true]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
           'data_class' =>LandUse::class
        ]);
    }


}