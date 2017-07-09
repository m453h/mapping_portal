<?php

namespace AppBundle\Form\AppUsers;

use AppBundle\Entity\AppUsers\UserRegion;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AppUserRegionFormType extends  AbstractType
{

    /**
     * @var EntityManager
     */
    private $entityManager;
    /**
     * @var RequestStack
     */
    private $requestStack;

    public function __construct(EntityManager $entityManager,RequestStack $requestStack)
    {
        $this->entityManager = $entityManager;
        $this->requestStack = $requestStack;
    }


    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {

            $form = $event->getForm();

            $userId = $this->requestStack->getCurrentRequest()->get('userId');

            $selectedRegions = $this->entityManager->getRepository('AppBundle:AppUsers\UserRegion')
                ->getAssignedRegionsToUser($userId);

            $availableRegions = $this->entityManager->getRepository('AppBundle:AppUsers\UserRegion')
                ->getAvailableRegions();

            $form->add('region', ChoiceType::class, array(
                'placeholder' => 'Choose a region',
                'multiple' =>true,
                'expanded'=>false,
                'choices'  => $availableRegions,
                'data' => $selectedRegions,
                'attr'=>['class'=>'select2-basic'],
                'label'=>'Regions available',
                'required'=>false
            ));

        }
        );



    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' =>UserRegion::class
        ]);
    }


}