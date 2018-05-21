<?php

namespace AppBundle\Controller\Administration\DataCollector;


use AppBundle\Entity\DataCollector\User;
use AppBundle\Form\DataCollector\DataCollectorFormType;
use AppBundle\Form\DataCollector\ResetPasswordForm;
use Pagerfanta\Adapter\DoctrineDbalAdapter;
use Pagerfanta\Pagerfanta;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;


class DataCollectorController extends Controller
{


    /**
     * @Route("/administration/data-collectors", name="data_collectors_list")
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
        $options['username'] = $request->query->get('username');
        $options['fullName'] = $request->query->get('fullName');

        $maxPerPage = $this->getParameter('grid_per_page_limit');

        $em = $this->getDoctrine()->getManager();

        $qb1 = $em->getRepository('AppBundle:DataCollector\User')
            ->findAllUsers($options);

        $qb2 = $em->getRepository('AppBundle:DataCollector\User')
            ->countAllUsers($qb1);

        $adapter =new DoctrineDbalAdapter($qb1,$qb2);
        $dataGrid = new Pagerfanta($adapter);
        $dataGrid->setMaxPerPage($maxPerPage);
        $dataGrid->setCurrentPage($page);
        $dataGrid->getCurrentPageResults();

        //Configure the grid
        $grid = $this->get('app.helper.grid_builder');
        $grid->addGridHeader('S/N',null,'index');
        $grid->addGridHeader('Username','username','text',true);
        $grid->addGridHeader('Full name','fullName','text',true);
        $grid->addGridHeader('Mobile','startDate','text',false);
        $grid->addGridHeader('Actions',null,'action');
        $grid->setStartIndex($page,$maxPerPage);
        $grid->setPath('data_collectors_list');
        $grid->setSecondaryPath('app_user_region_list');
        $grid->setIgnoredButtons(["more","approve","decline"]);
        $grid->setCurrentObject($class);
        $grid->setButtons();

        //Render the output
        return $this->render(
            'administration/main/app.list.html.twig',array(
            'records'=>$dataGrid,
            'grid'=>$grid,
            'title'=>'Existing Data Collectors',
            'gridTemplate'=>'administration/lists/data.collector/data.collectors.list.html.twig'
        ));
    }



    /**
     * @Route("/administration/data-collectors/add", name="data_collectors_add")
     * @param Request $request
     * @return Response
     */
    public function newAction(Request $request)
    {
        $class = get_class($this);

        $this->denyAccessUnlessGranted('add',$class);

        $form = $this->createForm(DataCollectorFormType::class);

        // only handles data on POST
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $appUser = $form->getData();
            $em = $this->getDoctrine()->getManager();

            $encoder = $this->get('security.password_encoder');
            $appUser->setPassword($encoder->encodePassword($this->getUser(),$appUser->getPassword()));
            $appUser->setAccountStatus('A');
            $em->persist($appUser);
            $em->flush();

            $this->addFlash('success','Data collector successfully created');

            return $this->redirectToRoute('data_collectors_list');
        }

        return $this->render(
            'administration/main/app.form.html.twig',
            array(
                'formTemplate'=>'data.collector/user',
                'form'=>$form->createView(),
                'title'=>'Data Collector Details',
            )

        );
    }


    /**
     * @Route("/administration/data-collectors/edit/{userId}", name="data_collectors_edit")
     * @param Request $request
     * @param User $user
     * @return Response
     */
    public function editAction(Request $request,User $user)
    {
        $class = get_class($this);

        $this->denyAccessUnlessGranted('add',$class);

        $form = $this->createForm(DataCollectorFormType::class,$user);

        // only handles data on POST
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $appUser = $form->getData();
            $em = $this->getDoctrine()->getManager();
            $em->persist($appUser);
            $em->flush();

            $this->addFlash('success','Data collector successfully updated');

            return $this->redirectToRoute('data_collectors_list');
        }

        return $this->render(
            'administration/main/app.form.html.twig',
            array(
                'formTemplate'=>'data.collector/user.edit',
                'form'=>$form->createView(),
                'title'=>'Data Collector Details',
            )

        );
    }


    /**
     * @Route("/administration/data-collectors/delete/{Id}", name="data_collectors_delete")
     * @param $Id
     * @return Response
     * @internal param Request $request
     */
    public function deleteAction($Id)
    {
        $class = get_class($this);

        $this->denyAccessUnlessGranted('delete',$class);

        $em = $this->getDoctrine()->getManager();

        $user = $em->getRepository('AppBundle:DataCollector\User')->find($Id);

        if($user instanceof User)
        {
            $em->remove($user);
            $em->flush();
            $this->addFlash('success', 'Data collector successfully removed !');
        }
        else
        {
            $this->addFlash('error', 'Data collector  not found !');
        }


        return $this->redirectToRoute('data_collectors_list');

    }




    /**
     * @Route("/administration/update-app-user-status/block/{Id}", name="data_collector_block",defaults={"Id":0})
     * @param $Id
     * @internal param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function blockAccountAction($Id)
    {
        $class = get_class($this);

        $em = $this->getDoctrine()->getManager();

        $user = $em->getRepository('AppBundle:DataCollector\User')->find($Id);

        $this->denyAccessUnlessGranted('decline',$class);
        $action = 'blocked';
        $status = 'B';

        $accountStatus =  $user->getAccountStatus();

        if($user instanceof User  && $accountStatus != 'I')
        {
            $user->setAccountStatus($status);

            $em->flush();

            $this->addFlash('success', sprintf('Data collector account successfully %s !',$action));
        }
        else if($accountStatus == 'I')
        {
            $this->addFlash('warning', 'Inactive Data collector account status can not be modified !');
        }
        else
        {
            $this->addFlash('error', 'Data collector account not found !');
        }

        return $this->redirectToRoute('data_collectors_info',['Id'=>$Id]);
    }


    /**
     * @Route("/administration/update-app-user-status/un-block/{Id}", name="data_collector_unblock",defaults={"Id":0})
     * @param $Id
     * @internal param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function unBlockAccountAction($Id)
    {
        $class = get_class($this);

        $em = $this->getDoctrine()->getManager();

        $user = $em->getRepository('AppBundle:DataCollector\User')->find($Id);

        $this->denyAccessUnlessGranted('approve',$class);

        $action = 'un-blocked';
        $status = 'A';

        $accountStatus =  $user->getAccountStatus();

        if($user instanceof User  && $accountStatus != 'I')
        {
            $user->setAccountStatus($status);

            $em->flush();

            $this->addFlash('success', sprintf('Data collector account successfully %s !',$action));
        }
        else if($accountStatus == 'I')
        {
            $this->addFlash('warning', 'Active Data collector account status can not be modified !');
        }
        else
        {
            $this->addFlash('error', 'Data collector account not found !');
        }

        return $this->redirectToRoute('data_collectors_info',['Id'=>$Id]);
    }



    /**
     * @Route("/administration/app-user/reset-password/{id}", name="data_collector_password_reset",defaults={"id":0})
     * @param Request $request
     * @param User $user
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function resetPasswordAction(Request $request,User $user)
    {
        $this->denyAccessUnlessGranted('edit',get_class($this));

        $form = $this->createForm(ResetPasswordForm::class,$user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $user = $form->getData();

            $encoder = $this->get('security.password_encoder');
            $user->setPassword($encoder->encodePassword($this->getUser(),$user->getPassword()));

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
            $this->addFlash('success', 'Data collector password successfully updated!');

            return $this->redirectToRoute('data_collectors_list');
        }

        return $this->render(
            'administration/main/app.form.html.twig',
            array(
                'formTemplate'=>'data.collector/reset.password',
                'form'=>$form->createView(),
                'title'=>'Data collector account password reset'
            )

        );
    }


    /**
     * @Route("/administration/data-collectors/info/{Id}", name="data_collectors_info",defaults={"Id":0})
     * @param $Id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function detailsAction($Id)
    {
        $this->denyAccessUnlessGranted('view',get_class($this));

        $em = $this->getDoctrine()->getManager();

        $data = $em->getRepository('AppBundle:DataCollector\User')
            ->findOneBy(['userId'=>$Id]);

        if(!$data)
        {
            throw new NotFoundHttpException('Data collector Account Not Found');
        }
        else
        {

            $info = $this->get('app.helper.info_builder');

            $status = $data->getAccountStatus();

            switch($status)
            {
                case 'I':$status='Inactive';break;
                case 'A':$status='Active';break;
                case 'B':$status='Blocked';break;
                default :$status='Unknown';
            }

            $info->addTextElement('Username',$data->getUsername());
            $info->addTextElement('Full name',$data->getFullName());
            $info->addTextElement('Mobile',$data->getMobile());
            $info->addTextElement('Account Status',$status);
            $info->addTextElement('Login Tries',$data->getLoginTries());

            $info->setLink('Activate Account','data_collector_unblock','activate-user',$Id);
            $info->setLink('Block Account','data_collector_block','block-user',$Id);
            $info->setLink('Reset Password','data_collector_password_reset','password',$Id);
            $info->setLink('Assign Regions','data_collector_region_list','module',$Id);

            $info->setPath('data_collectors_info');

            //Render the output
            return $this->render(
                'administration/main/app.info.html.twig',array(
                'info'=>$info,
                'title'=>'Data Collector Account Details',
                'infoTemplate'=>'base'
            ));
        }


    }

    
    /**
     * @Route("/administration/api/login", name="api_login")
     * @param Request $request
     * @return Response
     *
     */
    public function loginAction(Request $request)
    {
        $content =  $request->getContent();

        $data = json_decode($content,true);
        
        $em = $this->getDoctrine()->getManager();

        $data['status'] = 'FAIL-A';

        $username = null;

        if(isset($data['username']))
        {
            $username = $data['username'];
        }

        $appUser = $em->getRepository('AppBundle:DataCollector\User')->findOneBy(['username'=>$username]);

        if($appUser instanceof User)
        {
            $user = new \AppBundle\Entity\UserAccounts\User();

            $encoderService = $this->get('security.encoder_factory');

            $encoder = $encoderService->getEncoder($user);

            if ($encoder->isPasswordValid($appUser->getPassword(), $data['password'], $user->getSalt()))
            {

                if($appUser->getAccountStatus()=='A')
                {
                    $data['status'] = 'PASS';
                    $data['token'] = base64_encode(random_bytes(64));
                    $userId = $appUser->getUserId();

                    $data['regions'] = $em->getRepository('AppBundle:Location\Region')
                        ->findAllRegions(['sortBy'=>'region_name','sortType'=>'ASC','userId'=>$userId])
                        ->execute()
                        ->fetchAll();

                    $data['districts'] = $em->getRepository('AppBundle:Location\District')
                        ->findAllDistricts(['sortBy'=>'district_name','sortType'=>'ASC','userId'=>$userId])
                        ->select('district_id,district_name,r.region_id')
                        ->execute()
                        ->fetchAll();


                    $data['wards'] = $em->getRepository('AppBundle:Location\Ward')
                        ->findAllWards(['sortBy'=>'ward_name','sortType'=>'ASC','userId'=>$userId])
                        ->select('ward_id,ward_name,w.district_id')
                        ->execute()
                        ->fetchAll();
                   
                }
                else
                {
                    $data['status'] = 'FAIL-B';
                }

                $appUser->setToken($data['token']);
                $em->persist($appUser);
                $em->flush();
            }
        }



        unset($data['password']);

        return new JsonResponse($data);
    }



    /**
     * @Route("/administration/api/verifyToken", name="api_verify_token")
     * @param Request $request
     * @return Response
     *
     */
    public function verifyTokenAction(Request $request)
    {
        $content =  $request->getContent();

        $data = json_decode($content,true);
        
        $em = $this->getDoctrine()->getManager();

        $data['status'] = 'FAIL';

        $appUser = $em->getRepository('AppBundle:DataCollector\User')->findOneBy(['token'=>$data['token']]);

        if($appUser instanceof User)
        {
            $data['status'] = 'PASS';
        }

        unset($data['token']);

        return new JsonResponse($data);
    }



    /**
     * @Route("/administration/api/getInitialDataLoad", name="api_initial_data_load")
     * @return Response
     *
     */
    public function getDataLoadAction()
    {
        $em = $this->getDoctrine()->getManager();

        $data['status'] = 'PASS';

        $courtBuildingOwnershipStatus = $em->getRepository('AppBundle:Configuration\CourtBuildingOwnershipStatus')
            ->findAllCourtBuildingOwnerShipStatus(['sortBy'=>'description','sortType'=>'ASC'])
            ->execute()
            ->fetchAll();

        $levels = $em->getRepository('AppBundle:Configuration\CourtLevel')
            ->findAllCourtLevels(['sortBy'=>'description','sortType'=>'ASC'])
            ->execute()
            ->fetchAll();

        $courtBuildingStatus = $em->getRepository('AppBundle:Configuration\CourtBuildingStatus')
            ->findAllCourtBuildingOwnerStatus(['sortBy'=>'description','sortType'=>'ASC'])
            ->execute()
            ->fetchAll();

        $landOwnershipStatus = $em->getRepository('AppBundle:Configuration\LandOwnerShipStatus')
            ->findAllLandOwnerShipStatus(['sortBy'=>'description','sortType'=>'ASC'])
            ->execute()
            ->fetchAll();

        $zones = $em->getRepository('AppBundle:Configuration\Zone')
            ->findAllZones(['sortBy'=>'zone_name','sortType'=>'ASC'])
            ->execute()
            ->fetchAll();

        $activities = $em->getRepository('AppBundle:Configuration\EconomicActivity')
            ->findAllEconomicActivities(['sortBy'=>'description','sortType'=>'ASC'])
            ->execute()
            ->fetchAll();

        $transportModes = $em->getRepository('AppBundle:Configuration\TransportMode')
            ->findAllTransportModes(['sortBy'=>'description','sortType'=>'ASC'])
            ->execute()
            ->fetchAll();

        $environmentalStatus = $em->getRepository('AppBundle:Configuration\CourtEnvironmentalStatus')
            ->findAllCourtEnvironmentalStatus(['sortBy'=>'description','sortType'=>'ASC'])
            ->execute()
            ->fetchAll();

        $landUse = $em->getRepository('AppBundle:Configuration\LandUse')
            ->findAllLandUses(['sortBy'=>'description','sortType'=>'ASC'])
            ->execute()
            ->fetchAll();


        $data['message'] = 'downloadAction';
        $data['courtLevels'] = $levels;
        $data['courtBuildingOwnershipStatus'] = $courtBuildingOwnershipStatus;
        $data['courtBuildingStatus'] = $courtBuildingStatus;
        $data['landOwnershipStatus'] = $landOwnershipStatus;
        $data['zones'] = $zones;
        $data['economicActivities'] = $activities;
        $data['transportModes'] = $transportModes;
        $data['environmentalStatus'] = $environmentalStatus;
        $data['landUse'] = $landUse;

        return new JsonResponse($data);
    }



    /**
     * @Route("/administration/api/verifyDownloadVersion", name="api_download_version")
     * @return Response
     *
     */
    public function verifyDownloadVersionAction()
    {

        $em = $this->getDoctrine()->getManager();

        $results = $em->getRepository('AppBundle:DataCollector\DataVersion')
            ->findLatestDownloadVersion();
        
        $results['message'] = 'downloadVerification';

        return new JsonResponse($results);
    }





}