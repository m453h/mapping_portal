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
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CourtLocationFormType extends  AbstractType
{

    /**
     * @var ObjectManager
     */
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
        $builder
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
            ))
            ->add('courtLongitude',null,['required'=>false,'label'=>'Court Longitude'])
            ->add('courtLatitude',null,['required'=>false,'label'=>'Court Latitude'])
            ->add('courtCoordinatesDMS',null,['required'=>false,'label'=>'Court Coordinates (DMS)'])
            ->addEventSubscriber(new AddSelectedDataCourtForm($this->em));

        $builder->get('ward')->resetViewTransformers();

        $builder->get('ward')
            ->addModelTransformer(new WardToNumberTransformer($this->manager));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
           'data_class' =>Court::class
        ]);
    }


}