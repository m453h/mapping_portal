<?php

namespace AppBundle\Form\Reports;

use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MapReportFormType extends  AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('courtLevel', EntityType::class, [
                'placeholder' => 'Choose court level',
                'choice_label' => 'description',
                'mapped'=>false,
                'required'=>false,
                'class' => 'AppBundle\Entity\Configuration\CourtLevel',
                'query_builder' => function(EntityRepository  $er) {
                    return $er->createQueryBuilder('r')
                        ->orderBy('r.description', 'ASC');
                }
            ])
            ->add('region', EntityType::class, [
                'placeholder' => 'Choose a region',
                'choice_label' => 'regionName',
                'mapped'=>false,
                'required'=>false,
                'class' => 'AppBundle\Entity\Location\Region',
                'query_builder' => function(EntityRepository  $er) {
                    return $er->createQueryBuilder('r')
                        ->orderBy('r.regionName', 'ASC');
                }
            ])
            ->add('district', ChoiceType::class, array(
                'placeholder' => 'Choose District',
                'choices' => [],
                'mapped' => false,
                'required' => false
            ))
            ->add('ward', ChoiceType::class, array(
                'placeholder' => 'Choose Ward',
                'choices' => [],
                'mapped' => false,
                'required' => false
            ));

        $builder->get('region')->resetViewTransformers();
        $builder->get('district')->resetViewTransformers();
        $builder->get('ward')->resetViewTransformers();

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
        ]);
    }


}