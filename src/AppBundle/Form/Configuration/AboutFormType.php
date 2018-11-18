<?php


namespace AppBundle\Form\Configuration;

use AppBundle\Entity\Configuration\About;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AboutFormType extends  AbstractType
{


    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('textEn',null, [
                'required'=>false,
                'label'=>'Content (English)',
                'attr' => ['class' => 'markdown'],
            ])
            ->add('textSw',null, [
                'required'=>false,
                'label'=>'Content (Swahili)',
                'attr' => ['class' => 'markdown-sw'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' =>About::class
        ]);
    }

}