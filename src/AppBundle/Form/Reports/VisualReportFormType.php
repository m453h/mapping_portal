<?php

namespace AppBundle\Form\Reports;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class VisualReportFormType extends  AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('report', ChoiceType::class, array(
                'choices'  => array(
                    'REPORT 001 - COURTS PER CATEGORY DISTRIBUTION' => 1,
                    'REPORT 002 - TOP (10) REGIONS WITH AVERAGE CASES PER YEAR' => 2,
                    'REPORT 003 - ECONOMIC ACTIVITIES RELATION WITH REGIONS' => 3,
                ),
                'placeholder'=>'Select visual report template',
                'expanded'=>false,
                'label'=>'Report Template'
            ));
           
      
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
        ]);
    }


}