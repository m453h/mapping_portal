<?php

namespace AppBundle\Form\Reports;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PredefinedReportFormType extends  AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('report', ChoiceType::class, array(
                'choices'  => array(
                    'REPORT 001 - COURTS PER CATEGORY' => 1,
                    'REPORT 002 - COURT PER REGION PER WARD' => 2,
                ),
                'placeholder'=>'Select report template',
                'expanded'=>false,
                'label'=>'Report Template'
            ))
            ->add('preview', SubmitType::class, array(
                'label' => 'Preview form',
                'attr' =>['class'=>'btn-blue btn-preview']
            ))
            ->add('excel', SubmitType::class, array(
                'label' => 'Export to Excel',
                'attr' =>['class'=>'btn-green btn-excel']
            ))
            ->add('pdf', SubmitType::class, array(
                'label' => 'Export to PDF',
                'attr'=> ['class'=>'btn-red btn-pdf']
            ));
           
      
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
        ]);
    }


}