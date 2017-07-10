<?php

namespace AppBundle\Controller\AppUsers;


use AppBundle\Entity\AppUsers\User;
use AppBundle\Form\AppUsers\AppUserFormType;
use AppBundle\Form\AppUsers\ResetPasswordForm;
use Doctrine\DBAL\Exception\NotNullConstraintViolationException;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Pagerfanta\Adapter\DoctrineDbalAdapter;
use Pagerfanta\Pagerfanta;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;


class UserController extends Controller
{


    /**
     * @Route("/app-users", name="app_users_list")
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

        $maxPerPage = $this->getParameter('grid_per_page_limit');

        $em = $this->getDoctrine()->getManager();

        $qb1 = $em->getRepository('AppBundle:AppUsers\User')
            ->findAllAppUsers($options);

        $qb2 = $em->getRepository('AppBundle:AppUsers\User')
            ->countAllAppUsers($qb1);

        $adapter =new DoctrineDbalAdapter($qb1,$qb2);
        $dataGrid = new Pagerfanta($adapter);
        $dataGrid->setMaxPerPage($maxPerPage);
        $dataGrid->setCurrentPage($page);
        $dataGrid->getCurrentPageResults();

        //Configure the grid
        $grid = $this->get('app.helper.grid_builder');
        $grid->addGridHeader('S/N',null,'index');
        $grid->addGridHeader('Username','username','text',true);
        $grid->addGridHeader('Full name',null,'text',false);
        $grid->addGridHeader('Mobile','startDate','text',false);
        $grid->addGridHeader('Actions',null,'action');
        $grid->setStartIndex($page,$maxPerPage);
        $grid->setPath('app_users_list');
        $grid->setSecondaryPath('app_user_region_list');
        $grid->setIgnoredButtons(["more"]);
        $grid->setCurrentObject($class);
        $grid->setButtons();

        //Render the output
        return $this->render(
            'main/app.list.html.twig',array(
            'records'=>$dataGrid,
            'grid'=>$grid,
            'title'=>'Existing Application Users',
            'gridTemplate'=>'lists/app.users/app.users.list.html.twig'
        ));
    }



    /**
     * @Route("/app-users/add", name="app_user_add")
     * @param Request $request
     * @return Response
     */
    public function newAction(Request $request)
    {
        $class = get_class($this);

        $this->denyAccessUnlessGranted('add',$class);

        $form = $this->createForm(AppUserFormType::class);

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

            $this->addFlash('success','Application user successfully created');

            return $this->redirectToRoute('app_users_list');
        }

        return $this->render(
            'main/app.form.html.twig',
            array(
                'formTemplate'=>'app.users/user',
                'form'=>$form->createView(),
                'title'=>'Application User Details',
            )

        );
    }


    /**
     * @Route("/app-users/edit/{userId}", name="app_user_edit")
     * @param Request $request
     * @param User $user
     * @return Response
     */
    public function editAction(Request $request,User $user)
    {
        $class = get_class($this);

        $this->denyAccessUnlessGranted('add',$class);

        $form = $this->createForm(AppUserFormType::class,$user);

        // only handles data on POST
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $appUser = $form->getData();
            $em = $this->getDoctrine()->getManager();
            $em->persist($appUser);
            $em->flush();

            $this->addFlash('success','Application user successfully updated');

            return $this->redirectToRoute('app_users_list');
        }

        return $this->render(
            'main/app.form.html.twig',
            array(
                'formTemplate'=>'app.users/user.edit',
                'form'=>$form->createView(),
                'title'=>'Application User Details',
            )

        );
    }


    /**
     * @Route("/app-users/delete/{Id}", name="app_users_delete")
     * @param $Id
     * @return Response
     * @internal param Request $request
     */
    public function deleteAction($Id)
    {
        $class = get_class($this);

        $this->denyAccessUnlessGranted('delete',$class);

        $em = $this->getDoctrine()->getManager();

        $user = $em->getRepository('AppBundle:AppUsers\User')->find($Id);

        if($user instanceof User)
        {
            $em->remove($user);
            $em->flush();
            $this->addFlash('success', 'Application user successfully removed !');
        }
        else
        {
            $this->addFlash('error', 'Application user  not found !');
        }


        return $this->redirectToRoute('app_users_list');

    }




    /**
     * @Route("/update-app-user-status/block/{Id}", name="app_user_block",defaults={"Id":0})
     * @param $Id
     * @internal param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function blockAccountAction($Id)
    {
        $class = get_class($this);

        $em = $this->getDoctrine()->getManager();

        $user = $em->getRepository('AppBundle:AppUsers\User')->find($Id);

        $this->denyAccessUnlessGranted('decline',$class);
        $action = 'blocked';
        $status = 'B';

        $accountStatus =  $user->getAccountStatus();

        if($user instanceof User  && $accountStatus != 'I')
        {
            $user->setAccountStatus($status);

            $em->flush();

            $this->addFlash('success', sprintf('Application user account successfully %s !',$action));
        }
        else if($accountStatus == 'I')
        {
            $this->addFlash('warning', 'Inactive application user account status can not be modified !');
        }
        else
        {
            $this->addFlash('error', 'Application user account not found !');
        }

        return $this->redirectToRoute('app_user_info',['Id'=>$Id]);
    }


    /**
     * @Route("/update-app-user-status/un-block/{Id}", name="app_user_unblock",defaults={"Id":0})
     * @param $Id
     * @internal param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function unBlockAccountAction($Id)
    {
        $class = get_class($this);

        $em = $this->getDoctrine()->getManager();

        $user = $em->getRepository('AppBundle:AppUsers\User')->find($Id);

        $this->denyAccessUnlessGranted('approve',$class);

        $action = 'un-blocked';
        $status = 'A';

        $accountStatus =  $user->getAccountStatus();

        if($user instanceof User  && $accountStatus != 'I')
        {
            $user->setAccountStatus($status);

            $em->flush();

            $this->addFlash('success', sprintf('Application user account successfully %s !',$action));
        }
        else if($accountStatus == 'I')
        {
            $this->addFlash('warning', 'Active application user account status can not be modified !');
        }
        else
        {
            $this->addFlash('error', 'Application user account not found !');
        }

        return $this->redirectToRoute('app_user_info',['Id'=>$Id]);
    }



    /**
     * @Route("/app-user/reset-password/{id}", name="app_user_password_reset",defaults={"id":0})
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
            $this->addFlash('success', 'Application user password successfully updated!');

            return $this->redirectToRoute('app_users_list');
        }

        return $this->render(
            'main/app.form.html.twig',
            array(
                'formTemplate'=>'app.users/reset.password',
                'form'=>$form->createView(),
                'title'=>'Application user account password reset'
            )

        );
    }


    /**
     * @Route("/app-users/info/{Id}", name="app_user_info",defaults={"Id":0})
     * @param $Id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function detailsAction($Id)
    {
        $this->denyAccessUnlessGranted('view',get_class($this));

        $em = $this->getDoctrine()->getManager();

        $data = $em->getRepository('AppBundle:AppUsers\User')
            ->findOneBy(['userId'=>$Id]);

        if(!$data)
        {
            throw new NotFoundHttpException('Application User Account Not Found');
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

            $info->setLink('Activate Account','app_user_unblock','activate-user',$Id);
            $info->setLink('Block Account','app_user_block','block-user',$Id);
            $info->setLink('Reset Password','app_user_password_reset','password',$Id);
            $info->setLink('Assign Regions','app_user_region_list','module',$Id);

            $info->setPath('app_user_info');

            //Render the output
            return $this->render(
                'main/app.info.html.twig',array(
                'info'=>$info,
                'title'=>'Application User Account Details',
                'infoTemplate'=>'base'
            ));
        }


    }


    /**
     * @Route("/api/registration", name="api_registration")
     * @param Request $request
     * @return Response
     *
     */
    public function registrationAction(Request $request)
    {
        $content =  $request->getContent();

        //$content = '{"firstName":"Michael","surname":"Hudson","mobile":"a1","group":"Leaders","role":"Leader","isMarried":"Yes","password":"12"}';

        $data = json_decode($content,true);
        $this->get('logger')->error($content);
        $em = $this->getDoctrine()->getManager();

        $group = $em->getRepository('AppBundle:AppUsers\Group')->findOneBy(['groupName'=>$data['group']]);
        $role = $em->getRepository('AppBundle:AppUsers\Role')->findOneBy(['roleName'=>$data['role']]);

        $appUser = new User();

        $user = new \AppBundle\Entity\UserAccounts\User();


        $encoder = $this->container->get('security.password_encoder');
        $encoded = $encoder->encodePassword($user, $data['password']);

        $isMarried = false;

        if($data['isMarried'] == 'Yes')
        {
            $isMarried = true;
        }
        
        $appUser->setMobile($data['mobile']);
        $appUser->setIsMarried($isMarried);
        $appUser->setFirstName($data['firstName']);
        $appUser->setSurname($data['surname']);
        $appUser->setGroup($group);
        $appUser->setRole($role);
        $appUser->setUsername($data['mobile']);
        $appUser->setPassword($encoded);

        try
        {
            $em->persist($appUser);
            $em->flush();
            $data['status'] = 'PASS';
            $data['token'] = base64_encode(random_bytes(64));
            $appUser->setToken($data['token']);

        }
        catch (NotNullConstraintViolationException $e)
        {
            $data['status'] = 'FAIL';
        }
        catch (UniqueConstraintViolationException $e)
        {
            $data['status'] = 'FAIL-UNIQUE';
        }

        unset($data['password']);


        //Encode Password
        return new JsonResponse($data);
    }



    /**
     * @Route("/api/login", name="api_login")
     * @param Request $request
     * @return Response
     *
     */
    public function loginAction(Request $request)
    {
        $content =  $request->getContent();

        //$content = '{"username":"255654061261","password":"1234"}';

        $data = json_decode($content,true);
        $this->get('logger')->error($content);
        $em = $this->getDoctrine()->getManager();

        $data['status'] = 'FAIL';

        $username = null;

        if(isset($data['username']))
        {
            $username = $data['username'];
        }

        $appUser = $em->getRepository('AppBundle:AppUsers\User')->findOneBy(['username'=>$username]);

        if($appUser instanceof User)
        {
            $user = new \AppBundle\Entity\UserAccounts\User();

            $encoderService = $this->get('security.encoder_factory');

            $encoder = $encoderService->getEncoder($user);

            if ($encoder->isPasswordValid($appUser->getPassword(), $data['password'], $user->getSalt()))
            {
                $data['status'] = 'PASS';
                $data['token'] = base64_encode(random_bytes(64));
                $appUser->setToken($data['token']);
                $em->persist($appUser);
                $em->flush();
            }
        }

        unset($data['password']);

        return new JsonResponse($data);
    }



    /**
     * @Route("/api/verifyToken", name="api_verify_token")
     * @param Request $request
     * @return Response
     *
     */
    public function verifyTokenAction(Request $request)
    {
        $content =  $request->getContent();

        $data = json_decode($content,true);
        $this->get('logger')->error($content);
        $em = $this->getDoctrine()->getManager();

        $data['status'] = 'FAIL';

        $appUser = $em->getRepository('AppBundle:AppUsers\User')->findOneBy(['token'=>$data['token']]);

        if($appUser instanceof User)
        {
            $data['status'] = 'PASS';
        }

        unset($data['token']);

        return new JsonResponse($data);
    }



    /**
     * @Route("/api/getInitialDataLoad", name="api_initial_data_load")
     * @return Response
     *
     */
    public function getDataLoadAction()
    {
        $em = $this->getDoctrine()->getManager();

        $data['status'] = 'PASS';

        $regions = $em->getRepository('AppBundle:Location\Region')
            ->findAllRegions(['sortBy'=>'region_name','sortType'=>'ASC'])
            ->execute()
            ->fetchAll();

        $districts = $em->getRepository('AppBundle:Location\District')
            ->findAllDistricts(['sortBy'=>'district_name','sortType'=>'ASC'])
            ->select('district_id,district_name,region_name')
            ->execute()
            ->fetchAll();


        $wards = $em->getRepository('AppBundle:Location\Ward')
            ->findAllWards(['sortBy'=>'ward_name','sortType'=>'ASC'])
            ->select('ward_id,ward_name,w.district_id')
            ->execute()
            ->fetchAll();

        $courtBuildingOwnershipStatus = $em->getRepository('AppBundle:Configuration\CourtBuildingOwnershipStatus')
            ->findAllCourtBuildingOwnerShipStatus(['sortBy'=>'description','sortType'=>'ASC'])
            ->execute()
            ->fetchAll();


        $courtBuildingStatus = $em->getRepository('AppBundle:Configuration\CourtBuildingStatus')
            ->findAllCourtBuildingOwnerStatus(['sortBy'=>'description','sortType'=>'ASC'])
            ->execute()
            ->fetchAll();

        $courtCategories = $em->getRepository('AppBundle:Configuration\CourtCategory')
            ->findAllCourtCategories(['sortBy'=>'description','sortType'=>'ASC'])
            ->execute()
            ->fetchAll();

        $courtLevels = $em->getRepository('AppBundle:Configuration\CourtLevel')
            ->findAllCourtLevels(['sortBy'=>'description','sortType'=>'ASC'])
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



        $data['message'] = 'downloadAction';
        $data['regions'] = $regions;
        $data['districts'] = $districts;
        $data['wards'] = $wards;
        $data['courtCategories'] = $courtCategories;
        $data['courtBuildingOwnershipStatus'] = $courtBuildingOwnershipStatus;
        $data['courtBuildingStatus'] = $courtBuildingStatus;
        $data['courtLevels'] = $courtLevels;
        $data['landOwnershipStatus'] = $landOwnershipStatus;
        $data['zones'] = $zones;


        return new JsonResponse($data);
    }



    /**
     * @Route("/api/verifyDownloadVersion", name="api_download_version")
     * @return Response
     *
     */
    public function verifyDownloadVersionAction()
    {

        $em = $this->getDoctrine()->getManager();

        $results = $em->getRepository('AppBundle:AppUsers\DataVersion')
            ->findLatestDownloadVersion();
        
        $results['message'] = 'downloadVerification';

        return new JsonResponse($results);
    }





}