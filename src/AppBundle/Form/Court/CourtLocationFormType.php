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
            ->add('isPlotOnly',null,['required'=>false,'label'=>'This is a record of a plot only'])
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