<?php


namespace AppBundle\Form\EventListener;

use AppBundle\Form\CustomField\IgnoreChoiceType;
use Doctrine\ORM\EntityManager;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\Debug\Exception\ContextErrorException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class AddSelectedDataCourtForm implements EventSubscriberInterface
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
        $court = $event->getData();

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
        
        if ($court!=null)
        {
            $ward = (object) $court->getWard();

            $district = (object) $ward->getDistrict();

            $region = (object) $district->getRegion();


            $wards = $this->em->getRepository('AppBundle:Location\Ward')
                ->findAllWards(['sortType'=>'ASC','sortBy'=>null,'districtId'=>$district->getDistrictId(),'userId'=>null])
                ->execute()
                ->fetchAll();

            $wardData = [];

            foreach ($wards as $item)
            {
                $wardData[$item['ward_name']] = $item['ward_id'];
            }

            $form->add('region', IgnoreChoiceType::class, array(
                'placeholder' => 'Choose region',
                'choices'  => $regionData,
                'data' =>$region->getRegionId()
            ));
            
            $form->add('district', IgnoreChoiceType::class, array(
                'placeholder' => 'Choose district',
                'choices'  => [$district->getDistrictName() => $district->getDistrictId()],
                'data'=>$district->getDistrictId(),
            ));

            $form->add('ward', IgnoreChoiceType::class, array(
                'placeholder' => 'Choose ward',
                'choices'  => $wardData,
                'data'=>$ward->getWardId(),
            ));


        }




    }
    

}