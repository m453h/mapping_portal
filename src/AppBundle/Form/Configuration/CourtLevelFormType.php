<?php

namespace AppBundle\Form\Configuration;

use AppBundle\Entity\Configuration\CourtCategory;
use AppBundle\Entity\Configuration\CourtLevel;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CourtLevelFormType extends  AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('description',null,['required'=>true])
            ->add('descriptionSw',null,['required'=>true])
            ->add('hierarchy',null,['required'=>true])
            ->add('details',null, [
                'required'=>false,
                'attr' => ['class' => 'markdown'],
            ])
            ->add('detailsSw',null, [
                'required'=>false,
                'attr' => ['class' => 'markdown-sw'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
           'data_class' =>CourtLevel::class
        ]);
    }


}