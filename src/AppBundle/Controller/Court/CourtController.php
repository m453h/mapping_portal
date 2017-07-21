<?php

namespace AppBundle\Controller\Court;

use AppBundle\Entity\Configuration\CourtBuildingOwnershipStatus;
use AppBundle\Entity\Configuration\EconomicActivity;
use AppBundle\Entity\Court\Court;
use AppBundle\Entity\Court\CourtEconomicActivities;
use AppBundle\Entity\Court\CourtLandUse;
use AppBundle\Entity\Court\CourtTransportModes;
use Pagerfanta\Adapter\DoctrineDbalAdapter;
use Pagerfanta\Pagerfanta;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


class CourtController extends Controller
{

    /**
     * @Route("/court-building-ownership-status", name="court_list")
     * @param Request $request
     * @return Response
     *
     */
    public function listAction(Request $request)
    {
        $class = get_class($this);
        
        $this->denyAccessUnlessGranted('view',$class);

        $page = $request->query->get('page',1);
        $options['sortBy'] = $request->query->get('sortBy');
        $options['sortType'] = $request->query->get('sortType');
        $options['name'] = $request->query->get('name');

        $maxPerPage = $this->getParameter('grid_per_page_limit');

        $em = $this->getDoctrine()->getManager();

        $qb1 = $em->getRepository('AppBundle:Configuration\CourtBuildingOwnershipStatus')
            ->findAllCourtBuildingOwnerShipStatus($options);

        $qb2 = $em->getRepository('AppBundle:Configuration\CourtBuildingOwnershipStatus')
            ->countAllLandOwnerShipStatus($qb1);

        $adapter =new DoctrineDbalAdapter($qb1,$qb2);
        $dataGrid = new Pagerfanta($adapter);
        $dataGrid->setMaxPerPage($maxPerPage);
        $dataGrid->setCurrentPage($page);
        $dataGrid->getCurrentPageResults();
        
        //Configure the grid
        $grid = $this->get('app.helper.grid_builder');
        $grid->addGridHeader('S/N',null,'index');
        $grid->addGridHeader('Description','name','text',true);
        $grid->addGridHeader('Actions',null,'action');
        $grid->setStartIndex($page,$maxPerPage);
        $grid->setPath('court_building_ownership_status_list');
        $grid->setCurrentObject($class);
        $grid->setButtons();
        
        //Render the output
        return $this->render(
            'main/app.list.html.twig',array(
                'records'=>$dataGrid,
                'grid'=>$grid,
                'title'=>'Existing Court Building Ownership Status',
                'gridTemplate'=>'lists/base.list.html.twig'
        ));
    }



    /**
     * @Route("/api/submitCourtForm", name="api_court_form")
     * @param Request $request
     * @return Response
     *
     */
    public function courtFormAPIAction(Request $request)
    {
        $content =  $request->getContent();
        
        $data = json_decode($content,true);
        
        $coordinates = explode(',',$data['DECCourtCoordinates']);
        $data['DECCourtLatitude'] = $coordinates[0];
        $data['DECCourtLongitude'] = $coordinates[1];

        $coordinates = explode(',',$data['DECConnectivityCoordinates']);
        $data['DECConnectivityLatitude'] = $coordinates[0];
        $data['DECConnectivityLongitude'] = $coordinates[1];

        $em = $this->getDoctrine()->getManager();

        $user = $em->getRepository('AppBundle:AppUsers\User')
            ->findOneBy(['token'=>$data['authToken']]);

        $court = new Court();

        $level = $em->getRepository('AppBundle:Configuration\CourtLevel')
            ->findOneBy(['levelId'=>$data['courtLevelId']]);

        $ward = $em->getRepository('AppBundle:Location\Ward')
            ->findOneBy(['wardId'=>$data['wardId']]);

        $landOwnershipStatus = $em->getRepository('AppBundle:Configuration\LandOwnerShipStatus')
            ->findOneBy(['statusId'=>$data['landOwnershipStatusId']]);

        $buildingOwnershipStatus = $em->getRepository('AppBundle:Configuration\CourtBuildingOwnershipStatus')
            ->findOneBy(['statusId'=>$data['buildingOwnershipStatusId']]);

        $buildingStatus = $em->getRepository('AppBundle:Configuration\CourtBuildingStatus')
            ->findOneBy(['statusId'=>$data['buildingStatusId']]);

        $environmentalStatus = $em->getRepository('AppBundle:Configuration\CourtEnvironmentalStatus')
            ->findOneBy(['statusId'=>$data['environmentalStatusId']]);

        ($data['landSurveyStatus']=='1') ? $surveyStatus=true : $surveyStatus=false;

        ($data['titleDeedStatus']=='1') ? $titleDeedStatus=true : $titleDeedStatus=false;

        ($data['extensionPossibility']=='1') ? $extensionPossibility=true : $extensionPossibility=false;

        ($data['functionality']=='1') ? $functionality=true : $functionality=false;

        ($data['lastMileConnectivity']=='1') ? $lastMileConnectivity=true : $lastMileConnectivity=false;

        ($data['internetAvailability']=='1') ? $internetAvailability=true : $internetAvailability=false;

        $court->setCourtLevel($level);
        $court->setWard($ward);
        $court->setLandOwnershipStatus($landOwnershipStatus);
        $court->setIsLandSurveyed($surveyStatus);
        $court->setHasTitleDeed($titleDeedStatus);
        $court->setTitleDeed($data['titleDeedNo']);
        $court->setPlotNumber($data['plotNo']);
        $court->setBuildingOwnershipStatus($buildingOwnershipStatus);
        $court->setBuildingStatus($buildingStatus);
        $court->setHasExtensionPossibility($extensionPossibility);
        $court->setYearConstructed($data['yearConstructed']);
        $court->setMeetsFunctionality($data['functionality']);
        $court->setHasLastMileConnectivity($lastMileConnectivity);
        $court->setNumberOfComputers($data['numberOfComputers']);
        $court->setInternetAvailability($data['internetAvailability']);
        $court->setBandwidth($data['bandwidth']);
        $court->setAvailableSystems($data['availableSystems']);
        $court->setCasesPerYear($data['casesPerYear']);
        $court->setPopulationServed($data['populationServed']);
        $court->setNumberOfJustices($data['numberOfJustices']);
        $court->setNumberOfJudges($data['judges']);
        $court->setNumberOfResidentMagistrates($data['residentMagistrates']);
        $court->setNumberOfDistrictMagistrates($data['districtMagistrates']);
        $court->setNumberOfMagistrates($data['magistrates']);
        $court->setNumberOfCourtClerks($data['courtClerks']);
        $court->setNumberOfNonJudiciaryStaff($data['nonJudiciary']);
        $court->setEnvironmentalStatus($environmentalStatus);
        $court->setCourtCoordinatesDMS($data['DMSCourtCoordinates']);
        $court->setCourtLatitude($data['DECCourtLatitude']);
        $court->setCourtLongitude($data['DECCourtLongitude']);
        $court->setLastMileConnectivityDMS($data['DMSConnectivityCoordinates']);
        $court->setLastMileConnectivityLatitude($data['DECConnectivityLatitude']);
        $court->setLastMileConnectivityLongitude($data['DECConnectivityLongitude']);
        $court->setFibreDistance($data['fibreDistance']);
        $court->setAreasEntitled($data['areasEntitled']);
        $court->setUniqueCourtId($data['uniqueCourtId']);
        $court->setCreatedBy($user);

        $em->persist($court);
        $em->flush();

        $courtId = $court->getCourtId();

        $transportModes = explode(',',$data['transportModes']);
        $economicActivities = explode(',',$data['economicActivities']);
        $landUses = explode(',',$data['landUses']);

        foreach ($transportModes as $modeId)
        {
            $transportMode = $em->getRepository('AppBundle:Configuration\TransportMode')
                ->findOneBy(['modeId'=>$modeId]);

            $courtTransportMode = new CourtTransportModes();
            $courtTransportMode->setCourt($court);
            $courtTransportMode->setTransportMode($transportMode);
            $em->persist($courtTransportMode);
            $em->flush();
        }

        foreach ($economicActivities as $activityId)
        {
            $activity = $em->getRepository('AppBundle:Configuration\EconomicActivity')
                ->findOneBy(['activityId'=>$activityId]);

            $economicActivity = new CourtEconomicActivities();
            $economicActivity->setCourt($court);
            $economicActivity->setEconomicActivity($activity);
            $em->persist($economicActivity);
            $em->flush();
        }

        foreach ($landUses as $activityId)
        {
            $landUse = $em->getRepository('AppBundle:Configuration\LandUse')
                ->findOneBy(['activityId'=>$activityId]);

            $courtLandUse = new CourtLandUse();
            $courtLandUse->setCourt($court);
            $courtLandUse->setLandUse($landUse);
            $em->persist($courtLandUse);
            $em->flush();
        }

        $decoder = $this->get('app.helper.base_64_decoder');
       
        $uploadPath = $this->getParameter('court_images');

        $decoder->setUploadPath($uploadPath);

        $record = ['first'=>null,'second'=>null,'third'=>null,'fourth'=>null,'courtId'=>$courtId];

        if(!empty($data['courtBmpOne']))
        {
            $record['first'] = $decoder->decodeBase64($data['courtBmpOne']);
        }

        if(!empty($data['courtBmpTwo']))
        {
            $record['second'] = $decoder->decodeBase64($data['courtBmpTwo']);
        }

        if(!empty($data['courtBmpThree']))
        {
            $record['third'] = $decoder->decodeBase64($data['courtBmpThree']);
        }

        if(!empty($data['courtBmpFour']))
        {
            $record['fourth'] = $decoder->decodeBase64($data['courtBmpFour']);
        }

        $data['status'] = "PASS";
        
        $em->getRepository('AppBundle:Court\Court')
            ->updateCourtDetails($record,$courtId);

        //Encode Password
        return new JsonResponse($data);
    }












}