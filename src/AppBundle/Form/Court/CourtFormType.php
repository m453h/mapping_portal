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
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CourtFormType extends  AbstractType
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
            ->add('courtLevel', EntityType::class, [
                'placeholder' => 'Choose the court level',
                'choice_label' => 'description',
                'required'=>true,
                'class' => 'AppBundle\Entity\Configuration\CourtLevel',
                'query_builder' => function(EntityRepository $repo) {
                    return $repo->createQueryBuilder('l')
                        ->orderBy('l.description','ASC');
                }
            ])
            ->add('ward', EntityType::class, [
                'placeholder' => 'Choose the ward',
                'choice_label' => 'wardName',
                'required'=>true,
                'class' => 'AppBundle\Entity\Location\Ward',
                'query_builder' => function(EntityRepository $repo) {
                    return $repo->createQueryBuilder('w')
                        ->orderBy('w.wardName','ASC');
                }
            ])
            ->add('landOwnershipStatus', EntityType::class, [
                'placeholder' => 'Choose land ownership status',
                'choice_label' => 'description',
                'required'=>true,
                'class' => 'AppBundle\Entity\Configuration\LandOwnerShipStatus',
                'query_builder' => function(EntityRepository $repo) {
                    return $repo->createQueryBuilder('l')
                        ->orderBy('l.description','ASC');
                }
            ])
            ->add('isLandSurveyed',CheckboxType::class,['required'=>false])
            ->add('hasTitleDeed',CheckboxType::class,['required'=>false])
            ->add('titleDeed',null,['required'=>false])
            ->add('plotNumber',null,['required'=>false])
            ->add('buildingOwnershipStatus', EntityType::class, [
                'placeholder' => 'Choose building ownership status',
                'choice_label' => 'description',
                'required'=>true,
                'class' => 'AppBundle\Entity\Configuration\CourtBuildingOwnershipStatus',
                'query_builder' => function(EntityRepository $repo) {
                    return $repo->createQueryBuilder('b')
                        ->orderBy('b.description','ASC');
                }
            ])
            ->add('buildingStatus', EntityType::class, [
                'placeholder' => 'Choose building status',
                'choice_label' => 'description',
                'required'=>true,
                'class' => 'AppBundle\Entity\Configuration\CourtBuildingStatus',
                'query_builder' => function(EntityRepository $repo) {
                    return $repo->createQueryBuilder('b')
                        ->orderBy('b.description','ASC');
                }
            ])
            ->add('hasExtensionPossibility',CheckboxType::class,['required'=>false])
            ->add('yearConstructed',null,['required'=>false])
            ->add('meetsFunctionality',null,['required'=>false])
            ->add('hasLastMileConnectivity',CheckboxType::class,['required'=>false])
            ->add('numberOfComputers',null,['required'=>false])
            ->add('internetAvailability',CheckboxType::class,['required'=>false])
            ->add('bandwidth',null,['required'=>false])
            ->add('availableSystems',null,['required'=>false])
            ->add('casesPerYear',null,['required'=>false])
            ->add('populationServed',null,['required'=>false])
            ->add('numberOfJustices',null,['required'=>false])
            ->add('numberOfJudges',null,['required'=>false])
            ->add('numberOfResidentMagistrates',null,['required'=>false])
            ->add('numberOfDistrictMagistrates',null,['required'=>false])
            ->add('numberOfMagistrates',null,['required'=>false])
            ->add('numberOfCourtClerks',null,['required'=>false])
            ->add('numberOfNonJudiciaryStaff',null,['required'=>false])
            ->add('environmentalStatus', EntityType::class, [
                'placeholder' => 'Choose environmental status',
                'choice_label' => 'description',
                'required'=>true,
                'class' => 'AppBundle\Entity\Configuration\CourtEnvironmentalStatus',
                'query_builder' => function(EntityRepository $repo) {
                    return $repo->createQueryBuilder('e')
                        ->orderBy('e.description','ASC');
                }
            ])
            ->add('courtCoordinatesDMS',null,['required'=>false,'label'=>'Court Coordinates (DMS)'])
            ->add('lastMileConnectivityDMS',null,['required'=>false,'label'=>'Last Mile Connectivity Coordinates (DMS)'])
            ->add('courtLatitude',null,['required'=>false])
            ->add('courtLongitude',null,['required'=>false])
            ->add('lastMileConnectivityLatitude',null,['required'=>false])
            ->add('lastMileConnectivityLongitude',null,['required'=>false])
            ->add('fibreDistance',null,['required'=>false])
            ->add('areasEntitled',null,['required'=>false])
            ->add('landUseDescription',null,['required'=>false])
            ->add('economicActivitiesDescription',null,['required'=>false])
            ->add('transportModesDescription',null,['required'=>false])
            ->add('courtName',null,['required'=>false])
            ->add('courtStatus',null,['required'=>false,'label'=>'The court is operational'])
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