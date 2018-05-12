<?php

namespace AppBundle\Form\Location;

use AppBundle\Entity\Location\District;
use AppBundle\Entity\Location\Ward;
use AppBundle\Form\DataTransformer\DistrictToNumberTransformer;
use AppBundle\Form\EventListener\AddSelectedDataModuleAssignmentForm;
use AppBundle\Form\EventListener\AddSelectedDataWardForm;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class WardFormType extends  AbstractType
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
        $builder
            ->add('region', EntityType::class, [
                'placeholder' => 'Choose a region',
                'choice_label' => 'regionName',
                'mapped'=>false,
                'class' => 'AppBundle\Entity\Location\Region',
                'query_builder' => function(EntityRepository  $er) {
                    return $er->createQueryBuilder('r')
                        ->orderBy('r.regionName', 'ASC');
                }
            ])
            ->add('district', ChoiceType::class, array(
                'placeholder' => 'Choose District',
                'choices' => [],
                'mapped' => true,
                'required' => true
            ))
            ->add('wardName',null,['required'=>true])
            ->add('wardCode',null,['required'=>true])
            ->addEventSubscriber(new AddSelectedDataWardForm($this->em));


        $builder->get('district')->resetViewTransformers();

        $builder->get('district')
            ->addModelTransformer(new DistrictToNumberTransformer($this->manager));

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
           'data_class' =>Ward::class
        ]);
    }


}