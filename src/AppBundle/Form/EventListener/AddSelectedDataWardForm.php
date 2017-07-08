<?php


namespace AppBundle\Form\EventListener;

use AppBundle\Form\CustomField\IgnoreChoiceType;
use AppBundle\Form\DataTransformer\UserToNumberTransformer;
use Doctrine\ORM\EntityManager;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\Debug\Exception\ContextErrorException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class AddSelectedDataWardForm implements EventSubscriberInterface
{

    /**
     * @var EntityManager
     */
    private $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public static function getSubscribedEvents()
    {
        return array(FormEvents::PRE_SET_DATA => 'preSetData');
    }

    public function preSetData(FormEvent $event)
    {
        $ward = $event->getData();

        $form = $event->getForm();

        $regions = $this->em->getRepository('AppBundle:Location\Region')
            ->findAllRegions(['sortType'=>'ASC','sortBy'=>null])
            ->execute()
            ->fetchAll();

        $regionData = [];

        foreach ($regions as $region)
        {
            $regionData[$region['region_name']] = $region['region_id'];
        }
        
        if ($ward!=null)
        {
            $district = (object) $ward->getDistrict();

            $form->add('region', IgnoreChoiceType::class, array(
                'placeholder' => 'Choose region',
                'choices'  => $regionData,
            ));
            
            $form->add('district', IgnoreChoiceType::class, array(
                'placeholder' => 'Choose district',
                'choices'  => [$district->getDistrictName() => $district->getDistrictId()],
                'data'=>$district->getDistrictId(),
            ));


        }




    }
    

}