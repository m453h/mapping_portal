<?php

namespace AppBundle\Form\Location;

use AppBundle\Entity\Location\Region;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RegionFormType extends  AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('regionName',null,['required'=>true])
            ->add('regionCode',null,['required'=>true])
            ->add('zone', EntityType::class, [
                'placeholder' => 'Choose a zone',
                'choice_label' => 'zoneName',
                'mapped'=>true,
                'attr'=>['class'=>'select2-basic'],
                'class' => 'AppBundle\Entity\Location\Zone',
                'query_builder' => function(EntityRepository  $er) {
                    return $er->createQueryBuilder('z')
                        ->orderBy('z.zoneName', 'ASC');
                }
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
           'data_class' =>Region::class
        ]);
    }


}