<?php

namespace AppBundle\Form\Court;

use AppBundle\Entity\Court\Court;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichFileType;

class CourtImagesFormType extends  AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstCourtViewFile', VichFileType::class, array(
                'required'      => false,
                'allow_delete'  => false, // not mandatory, default is true
                'download_link' => false, // not mandatory, default is true
                'label'=>'First Court Image'
            ))
            ->add('secondCourtViewFile', VichFileType::class, array(
                'required'      => false,
                'allow_delete'  => false, // not mandatory, default is true
                'download_link' => false, // not mandatory, default is true
                'label'=>'Second Court Image'
            ))
            ->add('thirdCourtViewFile', VichFileType::class, array(
                'required'      => false,
                'allow_delete'  => false, // not mandatory, default is true
                'download_link' => false, // not mandatory, default is true
                'label'=>'Third Court Image'
            ))
            ->add('fourthCourtViewFile', VichFileType::class, array(
                'required'      => false,
                'allow_delete'  => false, // not mandatory, default is true
                'download_link' => false, // not mandatory, default is true
                'label'=>'Fourth Court Image'
            ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
           'data_class' =>Court::class
        ]);
    }


}