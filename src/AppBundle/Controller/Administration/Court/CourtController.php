<?php

namespace AppBundle\Controller\Administration\Court;

use AppBundle\Entity\Configuration\CourtBuildingOwnershipStatus;
use AppBundle\Entity\Configuration\EconomicActivity;
use AppBundle\Entity\Court\Court;
use AppBundle\Entity\Court\CourtEconomicActivities;
use AppBundle\Entity\Court\CourtLandUse;
use AppBundle\Entity\Court\CourtTransportModes;
use AppBundle\Form\Court\CourtBasicDetailsFormType;
use AppBundle\Form\Court\CourtBuildingDetailsFormType;
use AppBundle\Form\Court\CourtBuildingFacilitiesFormType;
use AppBundle\Form\Court\CourtFormType;
use AppBundle\Form\Court\CourtImagesFormType;
use AppBundle\Form\Court\CourtLandDetailsFormType;
use AppBundle\Form\Court\CourtLocationFormType;
use AppBundle\Form\Court\CourtStaffWorkLoadFormType;
use Doctrine\DBAL\Exception\DriverException;
use Pagerfanta\Adapter\DoctrineDbalAdapter;
use Pagerfanta\Pagerfanta;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;


class CourtController extends Controller
{

    /**
     * @Route("/administration/court-form", name="court_form_list")
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
        $options['courtName'] = $request->query->get('courtName');
        $options['courtLevel'] = $request->query->get('courtLevel');
        $options['location'] = $request->query->get('location');

        $maxPerPage = $this->getParameter('grid_per_page_limit');

        $em = $this->getDoctrine()->getManager();

        $qb1 = $em->getRepository('AppBundle:Court\Court')
            ->findAllCourts($options);

        $qb2 = $em->getRepository('AppBundle:Court\Court')
            ->countAllCourts($qb1);

        $adapter =new DoctrineDbalAdapter($qb1,$qb2);
        $dataGrid = new Pagerfanta($adapter);
        $dataGrid->setMaxPerPage($maxPerPage);
        $dataGrid->setCurrentPage($page);
        $dataGrid->getCurrentPageResults();

        //Configure the grid
        $grid = $this->get('app.helper.grid_builder');
        $grid->addGridHeader('S/N',null,'index');
        $grid->addGridHeader('Court Name','courtName','text',true);
        $grid->addGridHeader('Court Level','courtLevel','text',true);
        $grid->addGridHeader('Location','location','text',true);
        $grid->addGridHeader('Data Type',null,'text',false);
        $grid->addGridHeader('Verification',null,'text',false);
        $grid->addGridHeader('Actions',null,'action');
        $grid->setStartIndex($page,$maxPerPage);
        $grid->setPath('court_form_list');
        $grid->setIgnoredButtons(['add']);
        $grid->setCurrentObject($class);
        $grid->setButtons();

        //Render the output
        return $this->render(
            'administration/main/app.list.html.twig',array(
            'records'=>$dataGrid,
            'grid'=>$grid,
            'title'=>'Existing Court List',
            'gridTemplate'=>'administration/lists/court/court.list.html.twig'
        ));
    }


    /**
     * @Route("/administration/court-form/info/{courtId}", name="court_form_info",defaults={"courtId":0})
     * @param $courtId
     * @return Response
     * @throws NotFoundHttpException
     */
    public function detailsAction($courtId)
    {
        $class = get_class($this);

        $this->denyAccessUnlessGranted('view',$class);

        $em = $this->getDoctrine()->getManager();

        $data = $em->getRepository('AppBundle:Court\Court')
            ->find($courtId);

        $data->getIsLandSurveyed() === true ? $data->setIsLandSurveyed("YES"): $data->setIsLandSurveyed("NO") ;

        $data->getHasTitleDeed() === true ?  $data->setHasTitleDeed("YES"): $data->setHasTitleDeed("NO") ;

        $data->getHasExtensionPossibility() === true ? $data->setHasExtensionPossibility("YES") : $data->setHasExtensionPossibility("NO");

        $data->getMeetsFunctionality() === true ? $data->setMeetsFunctionality("YES"): $data->setMeetsFunctionality("NO");

        $data->getHasLastMileConnectivity() === true ? $data->setHasLastMileConnectivity("YES"): $data->setHasLastMileConnectivity("NO");

        $data->getInternetAvailability() === true ? $data->setInternetAvailability("YES") : $data->setInternetAvailability("NO") ;

        if(!$data)
        {
            throw new NotFoundHttpException('Court Not Found');
        }

        $info = $this->get('app.helper.info_builder');



        //Work Load
        $info->addTextElement('Available Systems',$data->getAvailableSystems());
        $info->addTextElement('Cases Per Year',$data->getCasesPerYear());
        $info->addTextElement('Population Served',$data->getPopulationServed());

       //Staffing
        $info->addTextElement('Staff Number',sprintf('Justices: %s, Judges: %s, Resident Magistrates: %s, District Magistrates: %s, Magistrates: %s, Court Clerks: %s, Non Judiciary Staff: %s.',
            $info->parseNumber($data->getNumberOfJustices()),
            $info->parseNumber($data->getNumberOfJudges()),
            $info->parseNumber($data->getNumberOfResidentMagistrates()),
            $info->parseNumber($data->getNumberOfDistrictMagistrates()),
            $info->parseNumber($data->getNumberOfMagistrates()),
            $info->parseNumber($data->getNumberOfCourtClerks()),
            $info->parseNumber($data->getNumberOfNonJudiciaryStaff())
        ));


        //Other Details
        $economicActivities = $em->getRepository('AppBundle:Court\Court')
            ->findEconomicActivitiesByCourtId($courtId);

        $landUses = $em->getRepository('AppBundle:Court\Court')
            ->findLandUseByCourtId($courtId);

        $transportModes = $em->getRepository('AppBundle:Court\Court')
            ->findTransportModesByCourtId($courtId);

        $info->addTextElement('Economic Activities',$economicActivities);
        $info->addTextElement('More Details on Economic Activities',$data->getEconomicActivitiesDescription());
        $info->addTextElement('Transport Mode',$transportModes);
        $info->addTextElement('More Details on Transport Modes',$data->getTransportModesDescription());
        $info->addTextElement('Current Land Use',$landUses);
        $info->addTextElement('More Details on Land Use',$data->getLandUseDescription());
        $info->addTextElement('Environmental Status',$data->getEnvironmentalStatus()->getDescription());
        $info->addTextElement('Fibre Optic Termination Point Distance from Court',$data->getFibreDistance());
        $info->addTextElement('Areas entitled accessibility of justice',$data->getAreasEntitled());


        $coordinates['latitude'] = $data->getCourtLatitude();
        $coordinates['longitude'] = $data->getCourtLongitude();
        
        $info->setPath('court_form_info');

        $images = [];

        if($data->getFirstCourtView()!=null)
        {
            array_push($images, $data->getFirstCourtView());
        }

        if($data->getSecondCourtView()!=null)
        {
            array_push($images, $data->getSecondCourtView());
        }

        if($data->getThirdCourtView()!=null)
        {
            array_push($images, $data->getThirdCourtView());
        }

        if($data->getFourthCourtView()!=null)
        {
            array_push($images, $data->getFourthCourtView());
        }


        //Make decision if this user can edit details of this specific court

        $canEdit = false;

        $userRoles = $this->getUser()->getRoleIds();

        if(!empty($userRoles))
        {
            $acl =  $em->getRepository('AppBundle:UserAccounts\Permission')
                ->getCurrentUserACLs($class, $userRoles);

            if(in_array('edit',$acl))
            {
                $canEdit = true;
            }
        }



        //Render the output
        return $this->render(
            'administration/main/app.court.details.html.twig',array(
            'info'=>$info,
            'images'=>$images,
            'canEdit'=>$canEdit,
            'title'=>'Court Details',
            'court'=>$data,
            'coordinates'=>$coordinates,
            'infoTemplate'=>'base'
        ));
    }


    /**
     * @Route("/administration/court-form/edit/{courtId}", name="court_form_edit")
     * @param $courtId
     * @return Response
     */
    public function editAction($courtId)
    {
        $class = get_class($this);

        $this->denyAccessUnlessGranted('edit',$class);

        $this->addFlash('info','Select an information criteria you want to edit under this court');

        return $this->redirectToRoute('court_form_info',['courtId'=>$courtId]);
    }


    /**
     * @Route("/administration/court-basic-details-form/edit/{courtId}", name="court_basic_details_form_edit")
     * @param Request $request
     * @param Court $court
     * @return Response
     */
    public function basicDetailsAction(Request $request,Court $court)
    {
        $class = get_class($this);

        $this->denyAccessUnlessGranted('edit',$class);

        $form = $this->createForm(CourtBasicDetailsFormType::class,$court);

        // only handles data on POST
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $data = $form->getData();
            $em = $this->getDoctrine()->getManager();
            $em->persist($data);
            $em->flush();

            $this->addFlash('success','Court basic details successfully updated');

            $this->get('app.helper.audit_trail_logger')
                ->logUserAction('COURT\BASIC_DETAILS','EDIT',$court,$data);


            return $this->redirectToRoute('court_form_info',['courtId'=>$court->getCourtId()]);
        }

        return $this->render(
            'administration/main/app.form.html.twig',
            array(
                'formTemplate'=>'court/court.basic.details',
                'form'=>$form->createView(),
                'title'=>'Court Basic Details',
            )

        );
    }



    /**
     * @Route("/administration/court-building-details-form/edit/{courtId}", name="court_building_details_form_edit")
     * @param Request $request
     * @param Court $court
     * @return Response
     */
    public function buildingDetailsAction(Request $request,Court $court)
    {
        $class = get_class($this);

        $this->denyAccessUnlessGranted('edit',$class);

        $form = $this->createForm(CourtBuildingDetailsFormType::class,$court);

        // only handles data on POST
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $data = $form->getData();
            $em = $this->getDoctrine()->getManager();
            $em->persist($data);
            $em->flush();

            $this->addFlash('success','Court building details successfully updated');

            $this->get('app.helper.audit_trail_logger')
                ->logUserAction('COURT\BUILDING_DETAILS','EDIT',$court,$data);



            return $this->redirectToRoute('court_form_info',['courtId'=>$court->getCourtId()]);
        }

        return $this->render(
            'administration/main/app.form.html.twig',
            array(
                'formTemplate'=>'court/court.building.details',
                'form'=>$form->createView(),
                'title'=>'Court Building Details',
            )

        );
    }



    /**
     * @Route("/administration/court-location-details-form/edit/{courtId}", name="court_location_details_form_edit")
     * @param Request $request
     * @param Court $court
     * @return Response
     */
    public function courtLocationDetailsAction(Request $request,Court $court)
    {
        $class = get_class($this);

        $this->denyAccessUnlessGranted('edit',$class);

        $form = $this->createForm(CourtLocationFormType::class,$court);

        // only handles data on POST
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $data = $form->getData();
            $em = $this->getDoctrine()->getManager();
            $em->persist($data);
            $em->flush();

            $this->addFlash('success','Court location details successfully updated');

            $this->get('app.helper.audit_trail_logger')
                ->logUserAction('COURT\LOCATION_DETAILS','EDIT',$court,$data);



            return $this->redirectToRoute('court_form_info',['courtId'=>$court->getCourtId()]);
        }

        return $this->render(
            'administration/main/app.form.html.twig',
            array(
                'formTemplate'=>'court/court.building.details',
                'form'=>$form->createView(),
                'title'=>'Court Building Details',
            )

        );
    }



    /**
     * @Route("/administration/court-building-facilities-form/edit/{courtId}", name="court_building_facilities_form_edit")
     * @param Request $request
     * @param Court $court
     * @return Response
     */
    public function buildingFacilitiesAction(Request $request,Court $court)
    {
        $class = get_class($this);

        $this->denyAccessUnlessGranted('edit',$class);

        $form = $this->createForm(CourtBuildingFacilitiesFormType::class,$court);

        // only handles data on POST
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $data = $form->getData();
            $em = $this->getDoctrine()->getManager();
            $em->persist($data);
            $em->flush();

            $this->addFlash('success','Court building facilities details successfully updated');

            $this->get('app.helper.audit_trail_logger')
                ->logUserAction('COURT\BUILDING_FACILITIES','EDIT',$court,$data);

            return $this->redirectToRoute('court_form_info',['courtId'=>$court->getCourtId()]);
        }

        return $this->render(
            'administration/main/app.form.html.twig',
            array(
                'formTemplate'=>'court/court.building.facilities',
                'form'=>$form->createView(),
                'title'=>'Court Building Facilities Details',
            )

        );
    }



    /**
     * @Route("/administration/court-land-details-form/edit/{courtId}", name="court_land_details_form_edit")
     * @param Request $request
     * @param Court $court
     * @return Response
     */
    public function landDetailsFormAction(Request $request,Court $court)
    {
        $class = get_class($this);

        $this->denyAccessUnlessGranted('edit',$class);

        $form = $this->createForm(CourtLandDetailsFormType::class,$court);

        // only handles data on POST
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $data = $form->getData();
            $em = $this->getDoctrine()->getManager();
            $em->persist($data);
            $em->flush();

            $this->addFlash('success','Court land details successfully updated');

            $this->get('app.helper.audit_trail_logger')
                ->logUserAction('COURT\LAND_DETAILS','EDIT',$court,$data);

            return $this->redirectToRoute('court_form_info',['courtId'=>$court->getCourtId()]);
        }

        return $this->render(
            'administration/main/app.form.html.twig',
            array(
                'formTemplate'=>'court/court.land.details',
                'form'=>$form->createView(),
                'title'=>'Court Land Details',
            )

        );
    }




    /**
     * @Route("/administration/staff-work-load-form/edit/{courtId}", name="court_staff_workload_form_edit")
     * @param Request $request
     * @param Court $court
     * @return Response
     */
    public function staffWorkLoadFormAction(Request $request,Court $court)
    {
        $class = get_class($this);

        $this->denyAccessUnlessGranted('edit',$class);

        $form = $this->createForm(CourtStaffWorkLoadFormType::class,$court);

        // only handles data on POST
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $data = $form->getData();
            $em = $this->getDoctrine()->getManager();
            $em->persist($data);
            $em->flush();

            $this->addFlash('success','Court staff workload successfully updated');

            $this->get('app.helper.audit_trail_logger')
                ->logUserAction('COURT\COURT_BASIC_DETAILS','EDIT',$court,$data);


            return $this->redirectToRoute('court_form_info',['courtId'=>$court->getCourtId()]);
        }

        return $this->render(
            'administration/main/app.form.html.twig',
            array(
                'formTemplate'=>'court/court.staff.workload',
                'form'=>$form->createView(),
                'title'=>'Court Staff Workload Details',
            )

        );
    }



    /**
     * @Route("/administration/court-images-form/edit/{courtId}", name="court_images_form_edit")
     * @param Request $request
     * @param Court $court
     * @return Response
     */
    public function courtImagesFormAction(Request $request,Court $court)
    {
        $class = get_class($this);

        $this->denyAccessUnlessGranted('edit',$class);

        $form = $this->createForm(CourtImagesFormType::class,$court);

        // only handles data on POST
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $data = $form->getData();
            $em = $this->getDoctrine()->getManager();
            $em->persist($data);
            $em->flush();

            $this->addFlash('success','Court images successfully updated');
            /*$data->setFirstCourtViewFile(null);
            $data->setSecondCourtViewFile(null);
            $data->setThirdCourtViewFile(null);
            $data->setFourthCourtViewFile(null);
            $this->get('app.helper.audit_trail_logger')
                ->logUserAction('COURT\IMAGES','EDIT',$court,$data);
            */
            return $this->redirectToRoute('court_form_info',['courtId'=>$court->getCourtId()]);
        }

        return $this->render(
            'administration/main/app.form.html.twig',
            array(
                'formTemplate'=>'court/court.images',
                'form'=>$form->createView(),
                'court'=>$court,
                'isFullWidth'=>true,
                'title'=>'Court Images',
            )

        );
    }


    /**
     * @Route("/administration/court-status/{parentAction}/{action}/{courtId}", name="court_status_change")
     * @param Court $court
     * @param $parentAction
     * @param $action
     * @return Response* @internal param Request $request
     */
    public function activateLinkAction(Court $court,$parentAction,$action)
    {

        $class = get_class($this);

        $em = $this->getDoctrine()->getManager();

        $status = null;

        $actionDescriptor = strtoupper($action);
        $parentActionDescriptor = str_replace('-','_',strtoupper($parentAction));

        if($court instanceof Court)
        {
            if ($parentAction == 'court-status')
            {
                if ($action == 'validate')
                {
                    $this->denyAccessUnlessGranted('edit', $class);
                    $action = 'Court record status successfully marked as valid data';
                    $status = true;
                }
                else if ($action == 'invalidate')
                {
                    $this->denyAccessUnlessGranted('edit', $class);
                    $action = 'Court record status successfully marked as test data';
                    $status = false;
                }
                $court->setCourtRecordStatus($status);
            }
            else if ($parentAction == 'court-verification-status')
            {
                if ($action == 'verify')
                {
                    $this->denyAccessUnlessGranted('edit', $class);
                    $action = 'Court record successfully verified';
                    $status = true;
                }
                else if ($action == 'unverify')
                {
                    $this->denyAccessUnlessGranted('edit', $class);
                    $action = 'Court record successfully unverified';
                    $status = false;
                }

                $court->setCourtVerificationStatus($status);
            }

            $em->flush();

            $this->get('app.helper.audit_trail_logger')
                ->logUserAction('COURT\\'.$parentActionDescriptor,$actionDescriptor,null,$court);


            $this->addFlash('success', sprintf('%s !',$action));

        }
        else
        {
            $this->addFlash('error', 'Court not found !');
        }

        return $this->redirectToRoute('court_form_info',['courtId'=>$court->getCourtId()]);
    }


    /**
     * @Route("/administration/court-form/delete/{Id}", name="court_form_delete")
     * @param $Id
     * @return Response
     * @internal param Request $request
     */
    public function deleteAction($Id)
    {
        $class = get_class($this);

        $this->denyAccessUnlessGranted('delete',$class);

        $em = $this->getDoctrine()->getManager();

        $court = $em->getRepository('AppBundle:Court\Court')->find($Id);

        if($court instanceof Court)
        {
            $em->remove($court);
            $em->flush();
            $this->addFlash('success', 'Court successfully removed !');

            $this->get('app.helper.audit_trail_logger')
                ->logUserAction('COURT','REMOVE',$court,null);
        }
        else
        {
            $this->addFlash('error', 'Court not found !');
        }


        return $this->redirectToRoute('court_form_list');

    }



    /**
     * @Route("/administration/api/submitCourtForm", name="api_court_form")
     * @param Request $request
     * @return Response
     *
     */
    public function courtFormAPIAction(Request $request)
    {
        $content =  $request->getContent();

        try {
           /*$content = '{
                          "courtName": "Mahakama ya Kisutu",
                          "courtLevelId": "1",
                          "wardId": "3649",
                          "landOwnershipStatusId": "1",
                          "buildingOwnershipStatusId": "1",
                          "buildingStatusId": "3",
                          "environmentalStatusId": "1",
                          "authToken": "NDFyFENQG5INqGRWkjGKf2CdoKPTZTh4f0GPM5NBnGaZ5r9a7UnVduXeQLs9xnEUEBWiAdoHk1Bvkro6r3rlhQ==",
                          "uniqueCourtId": "1510871084431"
                        }';*/

            $data = json_decode($content, true);

            if (!empty($data['DECCourtCoordinates'])) {
                $coordinates = explode(',', $data['DECCourtCoordinates']);
                $data['DECCourtLatitude'] = $coordinates[0];
                $data['DECCourtLongitude'] = $coordinates[1];
            } else {
                $data['DECCourtCoordinates'] = null;
                $data['DECCourtCoordinates'] = null;
            }


            if (!empty($data['DECConnectivityCoordinates'])) {
                $coordinates = explode(',', $data['DECConnectivityCoordinates']);
                $data['DECConnectivityLatitude'] = $coordinates[0];
                $data['DECConnectivityLongitude'] = $coordinates[1];
            } else {
                $data['DECConnectivityLatitude'] = null;
                $data['DECConnectivityLongitude'] = null;
            }


            $em = $this->getDoctrine()->getManager();

            $user = $em->getRepository('AppBundle:AppUsers\User')
                ->findOneBy(['token' => $data['authToken']]);

            $court = new Court();

            $level = $em->getRepository('AppBundle:Configuration\CourtLevel')
                ->findOneBy(['levelId' => $data['courtLevelId']]);

            $ward = $em->getRepository('AppBundle:Location\Ward')
                ->findOneBy(['wardId' => $data['wardId']]);

            $landOwnershipStatus = $em->getRepository('AppBundle:Configuration\LandOwnerShipStatus')
                ->findOneBy(['statusId' => $this->getAPIParameter($data, 'landOwnershipStatusId')]);

            $buildingOwnershipStatus = $em->getRepository('AppBundle:Configuration\CourtBuildingOwnershipStatus')
                ->findOneBy(['statusId' => $this->getAPIParameter($data, 'buildingOwnershipStatusId')]);

            $buildingStatus = $em->getRepository('AppBundle:Configuration\CourtBuildingStatus')
                ->findOneBy(['statusId' => $this->getAPIParameter($data, 'buildingStatusId')]);

            $environmentalStatus = $em->getRepository('AppBundle:Configuration\CourtEnvironmentalStatus')
                ->findOneBy(['statusId' => $this->getAPIParameter($data, 'environmentalStatusId')]);

            ($this->getAPIParameter($data, 'landSurveyStatus') == '1') ? $surveyStatus = true : $surveyStatus = false;

            ($this->getAPIParameter($data, 'titleDeedStatus') == '1') ? $titleDeedStatus = true : $titleDeedStatus = false;

            ($this->getAPIParameter($data, 'extensionPossibility') == '1') ? $extensionPossibility = true : $extensionPossibility = false;

            ($this->getAPIParameter($data, 'functionality') == '1') ? $functionality = true : $functionality = false;

            ($this->getAPIParameter($data, 'lastMileConnectivity') == '1') ? $lastMileConnectivity = true : $lastMileConnectivity = false;

            ($this->getAPIParameter($data, 'internetAvailability') == '1') ? $internetAvailability = true : $internetAvailability = false;

            $court->setCourtLevel($level);
            $court->setWard($ward);
            $court->setLandOwnershipStatus($landOwnershipStatus);
            $court->setIsLandSurveyed($surveyStatus);
            $court->setHasTitleDeed($titleDeedStatus);
            $court->setTitleDeed($this->getAPIParameter($data, 'titleDeedNo'));
            $court->setPlotNumber($this->getAPIParameter($data, 'plotNo'));
            $court->setBuildingOwnershipStatus($buildingOwnershipStatus);
            $court->setBuildingStatus($buildingStatus);
            $court->setHasExtensionPossibility($extensionPossibility);
            $court->setYearConstructed($this->getAPIParameter($data, 'yearConstructed'));
            $court->setCourtName($this->getAPIParameter($data, 'courtName'));
            $court->setMeetsFunctionality($this->getAPIParameter($data, 'functionality'));
            $court->setHasLastMileConnectivity($lastMileConnectivity);
            $court->setNumberOfComputers($this->getAPIParameter($data, 'numberOfComputers'));
            $court->setInternetAvailability($this->getAPIParameter($data, 'internetAvailability'));
            $court->setBandwidth($this->getAPIParameter($data, 'bandwidth'));
            $court->setAvailableSystems($this->getAPIParameter($data, 'availableSystems'));
            $court->setCasesPerYear($this->getAPIParameter($data, 'casesPerYear'));
            $court->setPopulationServed($this->getAPIParameter($data, 'populationServed'));
            $court->setNumberOfJustices($this->getAPIParameter($data, 'numberOfJustices'));
            $court->setNumberOfJudges($this->getAPIParameter($data, 'judges'));
            $court->setNumberOfResidentMagistrates($this->getAPIParameter($data, 'residentMagistrates'));
            $court->setNumberOfDistrictMagistrates($this->getAPIParameter($data, 'districtMagistrates'));
            $court->setNumberOfMagistrates($this->getAPIParameter($data, 'magistrates'));
            $court->setNumberOfCourtClerks($this->getAPIParameter($data, 'courtClerks'));
            $court->setNumberOfNonJudiciaryStaff($this->getAPIParameter($data, 'nonJudiciary'));
            $court->setEnvironmentalStatus($environmentalStatus);
            $court->setCourtCoordinatesDMS($this->getAPIParameter($data, 'DMSCourtCoordinates'));
            $court->setCourtLatitude($this->getAPIParameter($data, 'DECCourtLatitude'));
            $court->setCourtLongitude($this->getAPIParameter($data, 'DECCourtLongitude'));
            $court->setLastMileConnectivityDMS($this->getAPIParameter($data, 'DMSConnectivityCoordinates'));
            $court->setLastMileConnectivityLatitude($this->getAPIParameter($data, 'DECConnectivityLatitude'));
            $court->setLastMileConnectivityLongitude($this->getAPIParameter($data, 'DECConnectivityLongitude'));
            $court->setFibreDistance($this->getAPIParameter($data, 'fibreDistance'));
            $court->setAreasEntitled($this->getAPIParameter($data, 'areasEntitled'));

            $court->setLandUseDescription($this->getAPIParameter($data, 'landUseDescription'));
            $court->setEconomicActivitiesDescription($this->getAPIParameter($data, 'economicActivitiesDescription'));
            $court->setTransportModesDescription($this->getAPIParameter($data, 'transportModesDescription'));

            $court->setUniqueCourtId($this->getAPIParameter($data, 'uniqueCourtId'));
            $court->setTimeCreated(new \Datetime());
            $court->setCreatedBy($user);

            $em->persist($court);
            $em->flush();

            $courtId = $court->getCourtId();

            $transportModes = explode(',', $this->getAPIParameter($data, 'transportModes'));
            $economicActivities = explode(',', $this->getAPIParameter($data, 'economicActivities'));
            $landUses = explode(',', $this->getAPIParameter($data, 'landUses'));

            foreach ($transportModes as $modeId) 
            {
                if (!empty($modeId)) 
                {
                    try 
                    {
                        $transportMode = $em->getRepository('AppBundle:Configuration\TransportMode')
                            ->findOneBy(['modeId' => $modeId]);

                        $courtTransportMode = new CourtTransportModes();
                        $courtTransportMode->setCourt($court);
                        $courtTransportMode->setTransportMode($transportMode);
                        $em->persist($courtTransportMode);
                        $em->flush();
                    }
                    catch (DriverException $ex)
                    {

                    }
                }
            }

            foreach ($economicActivities as $activityId) {

                if (!empty($activityId)) 
                {
                    try 
                    {
                        $activity = $em->getRepository('AppBundle:Configuration\EconomicActivity')
                            ->findOneBy(['activityId' => $activityId]);

                        $economicActivity = new CourtEconomicActivities();
                        $economicActivity->setCourt($court);
                        $economicActivity->setEconomicActivity($activity);
                        $em->persist($economicActivity);
                        $em->flush();
                    }
                    catch (DriverException $ex)
                    {
                        
                    }
                }
            }

            foreach ($landUses as $activityId) 
            {
                if (!empty($activityId)) 
                {
                    try 
                    {
                        $landUse = $em->getRepository('AppBundle:Configuration\LandUse')
                            ->findOneBy(['activityId' => $activityId]);

                        $courtLandUse = new CourtLandUse();
                        $courtLandUse->setCourt($court);
                        $courtLandUse->setLandUse($landUse);
                        $em->persist($courtLandUse);
                        $em->flush();
                    }
                    catch (DriverException $ex)
                    {

                    }
                }
            }

            $decoder = $this->get('app.helper.base_64_decoder');

            $uploadPath = $this->getParameter('court_images');

            $decoder->setUploadPath($uploadPath);

            $record = ['first' => null, 'second' => null, 'third' => null, 'fourth' => null, 'courtId' => $courtId];

            if (!empty($data['courtBmpOne'])) {
                $record['first'] = $decoder->decodeBase64($data['courtBmpOne']);
            }

            if (!empty($data['courtBmpTwo'])) {
                $record['second'] = $decoder->decodeBase64($data['courtBmpTwo']);
            }

            if (!empty($data['courtBmpThree'])) {
                $record['third'] = $decoder->decodeBase64($data['courtBmpThree']);
            }

            if (!empty($data['courtBmpFour'])) {
                $record['fourth'] = $decoder->decodeBase64($data['courtBmpFour']);
            }

            $data['status'] = "PASS";

            $em->getRepository('AppBundle:Court\Court')
                ->updateCourtDetails($record, $courtId);

        }
        catch(UniqueConstraintViolationException $e)
        {
            $data['status']="PASS";

            $em = $this->getDoctrine()->getManager();

            $decoder = $this->get('app.helper.base_64_decoder');

            $uploadPath = $this->getParameter('court_images');

            $decoder->setUploadPath($uploadPath);

            $court = $em->getRepository('AppBundle:Court\Court')
                ->findOneBy(['uniqueCourtId' => $this->getAPIParameter($data, 'uniqueCourtId')]);

            $courtId = $court->getCourtId();

            $record = ['first' => null, 'second' => null, 'third' => null, 'fourth' => null, 'courtId' => $courtId];

            if (!empty($data['courtBmpOne'])) {
                $record['first'] = $decoder->decodeBase64($data['courtBmpOne']);
            }

            if (!empty($data['courtBmpTwo'])) {
                $record['second'] = $decoder->decodeBase64($data['courtBmpTwo']);
            }

            if (!empty($data['courtBmpThree'])) {
                $record['third'] = $decoder->decodeBase64($data['courtBmpThree']);
            }

            if (!empty($data['courtBmpFour'])) {
                $record['fourth'] = $decoder->decodeBase64($data['courtBmpFour']);
            }

            $data['status'] = "PASS";

            $em->getRepository('AppBundle:Court\Court')
                ->updateCourtDetails($record, $courtId);

        }

        //Encode Password
        return new JsonResponse($data);
    }

    public function getAPIParameter($data, $name)
    {
        if(isset($data[$name]) && !empty($data[$name]))
        {
            return $data[$name];
        }

        return null;
    }



    /**
     * @Route("/administration/api/getIncompleteCourts", name="api_incomplete_court_list")
     * @param Request $request
     * @return Response
     *
     */
    public function getIncompleteCourtsAction(Request $request)
    {

        $content = $request->getContent();

        $data = json_decode($content, true);

        $data = $data[0];

        $em = $this->getDoctrine()->getManager();

        $records = $em->getRepository('AppBundle:Court\Court')
            ->findAllIncompleteCourts($data['authToken']);

        return new JsonResponse($records);
    }


    /**
     * @Route("/administration/api/submitCourtImages", name="api_court_image_form")
     * @param Request $request
     * @return Response
     *
     */
    public function courtFormImageAPIAction(Request $request)
    {

        $content =  $request->getContent();
        
        $data = json_decode($content, true);
        
        $em = $this->getDoctrine()->getManager();

        $decoder = $this->get('app.helper.base_64_decoder');

        $uploadPath = $this->getParameter('court_images');

        $decoder->setUploadPath($uploadPath);
        
        $courtId = $data['courtId'];

        $record = ['first' => null, 'second' => null, 'third' => null, 'fourth' => null, 'courtId' => $courtId];

        if (!empty($data['imageOne']))
        {
            $record['first'] = $decoder->decodeBase64($data['imageOne']);
        }

        if (!empty($data['imageTwo'])) 
        {
            $record['second'] = $decoder->decodeBase64($data['imageTwo']);
        }

        if (!empty($data['imageThree'])) 
        {
            $record['third'] = $decoder->decodeBase64($data['imageThree']);
        }

        if (!empty($data['imageFour'])) 
        {
            $record['fourth'] = $decoder->decodeBase64($data['imageFour']);
        }
        
        $em->getRepository('AppBundle:Court\Court')
            ->updateCourtDetails($record, $courtId);

        $data['status']="PASS";

        
        //Encode Password
        return new JsonResponse($data);
    }

    /**
     * @Route("/administration/api/getCourts",options={"expose"=true}, name="api_get_courts")
     * @param Request $request
     * @return JsonResponse
     */
    public function getCourtsAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $value = $request->get('searchTerm');

        $page = $request->get('page');

        $qb1 =  $em->getRepository('AppBundle:Court\Court')
            ->getCourtsByFilter($value);

        $qb2 = $em->getRepository('AppBundle:Court\Court')
            ->countAllCourts($qb1);

        $adapter =new DoctrineDbalAdapter($qb1,$qb2);
        $dataGrid = new Pagerfanta($adapter);
        $dataGrid->setMaxPerPage(100);
        $dataGrid->setCurrentPage($page);

        $data['results'] = $dataGrid->getCurrentPageResults();
        $data['pagination'] = ['more'=>$dataGrid->hasNextPage()];

        return new JsonResponse($data);
    }


}