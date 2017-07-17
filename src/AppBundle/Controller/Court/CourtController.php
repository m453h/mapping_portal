<?php

namespace AppBundle\Controller\Court;

use AppBundle\Entity\Configuration\CourtBuildingOwnershipStatus;
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
     * @Route("/court-building-ownership-status", name="court_building_ownership_status_list")
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
        $this->get('logger')->error($content);

        $coordinates = explode(',',$data['coordinates']);
        $data['latitude'] = $coordinates[0];
        $data['longitude'] = $coordinates[1];

        $em = $this->getDoctrine()->getManager();

        $em->getRepository('AppBundle:Court\Court')
            ->recordCourtDetails($data);

        /*
      
        $group = explode(',',$data['group']);
        
        $data['message'] = 'registration';

        $groupIds = $em->getRepository('AppBundle:AppUsers\Group')->findAllGroupIds();

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
        $appUser->setRole($role);
        $appUser->setAccountStatus('A');
        $appUser->setUsername($data['mobile']);
        $appUser->setPassword($encoded);

        try
        {
            $data['token'] = base64_encode(random_bytes(64));
            $appUser->setToken($data['token']);
            $em->persist($appUser);
            $em->flush();
            $data['status'] = 'PASS';
            $appUserId = $appUser->getUserId();
            $appUserGroupRepository = $em->getRepository('AppBundle:AppUsers\UserGroup');

            foreach ($group as $groupName)
            {
                $appUserGroupRepository->recordUserGroup($groupIds->get(trim($groupName)),$appUserId);
            }


        }
        catch (NotNullConstraintViolationException $e)
        {
            $data['status'] = 'FAIL';
        }
        catch (UniqueConstraintViolationException $e)
        {
            $data['status'] = 'FAIL-UNIQUE';
        }

        unset($data['password']);*/


        //Encode Password
        return new JsonResponse($data);
    }












}