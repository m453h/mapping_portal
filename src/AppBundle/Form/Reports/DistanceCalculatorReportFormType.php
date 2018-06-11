<?php

namespace AppBundle\Form\Reports;

use AppBundle\Form\DataTransformer\CourtToNumberTransformer;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DistanceCalculatorReportFormType extends  AbstractType
{


    private $manager;
    /**
     * @var EntityManager
     */
    private $em;


    public function __construct(ObjectManager $manager, EntityManager $em)
    {
        $this->manager = $manager;
        $this->em = $em;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        dump($builder->getData());


        $builder
            ->add('fromCourt', ChoiceType::class, array(
                'placeholder' => 'Type a court name, region or district or ward name',
                'attr'=>['class'=>'court-data-ajax from-court'],
                'mapped' => true,
                'required' => false
            ))

            ->add('toCourt', ChoiceType::class, array(
                'placeholder' => 'Type a court name, region or district or ward name',
                'choices' => [],
                'attr'=>['class'=>'court-data-ajax'],
                'mapped' => true,
                'required' => false
            ));

        $builder->get('fromCourt')->resetViewTransformers();
        $builder->get('toCourt')->resetViewTransformers();

        $builder->get('fromCourt')
            ->addModelTransformer(new CourtToNumberTransformer($this->manager));

        $builder->get('toCourt')
            ->addModelTransformer(new CourtToNumberTransformer($this->manager));

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
        ]);
    }


}