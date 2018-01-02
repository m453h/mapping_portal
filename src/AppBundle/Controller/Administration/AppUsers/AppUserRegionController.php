<?php

namespace AppBundle\Controller\Administration\AppUsers;


use AppBundle\Entity\AppUsers\User;
use AppBundle\Entity\AppUsers\UserRegion;
use AppBundle\Entity\Location\Region;
use AppBundle\Form\AppUsers\AppUserRegionFormType;
use Pagerfanta\Adapter\DoctrineDbalAdapter;
use Pagerfanta\Pagerfanta;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AppUserRegionController extends Controller
{

    /**
     * @Route("/administration/app-user-regions/{userId}", defaults={"userId" = null}, name="app_user_region_list")
     * @param Request $request
     * @param $userId
     * @return Response
     */
    public function eventGroupListAction(Request $request,$userId)
    {
        $class = get_class($this);
        
        $this->denyAccessUnlessGranted('view',$class);

        $page = $request->query->get('page',1);

        $em = $this->getDoctrine()->getManager();

        $appUser = $em->getRepository('AppBundle:AppUsers\User')
            ->find($userId);

        if ($appUser == null)
            throw $this->createNotFoundException('User not found');

        $this->addFlash('info','You can add or edit regions under the user: '.$appUser->getFullName().' at this stage.');
        
        $options['sortBy'] = $request->query->get('sortBy');
        $options['sortType'] = $request->query->get('sortType');
        $options['userId'] = $userId;


        $maxPerPage = $this->getParameter('grid_per_page_limit');

        $qb1 = $em->getRepository('AppBundle:AppUsers\UserRegion')
            ->findAllUserRegions($options);

        $qb2 = $em->getRepository('AppBundle:AppUsers\UserRegion')
            ->countAllUserRegions($qb1);

        $adapter =new DoctrineDbalAdapter($qb1,$qb2);
        $dataGrid = new Pagerfanta($adapter);
        $dataGrid->setMaxPerPage($maxPerPage);
        $dataGrid->setCurrentPage($page);
        $dataGrid->getCurrentPageResults();

        //Configure the grid
        $grid = $this->get('app.helper.grid_builder');
        $grid->addGridHeader('S/N',null,'index');
        $grid->addGridHeader('Region Name','name','text',false);
        $grid->addGridHeader('Actions',null,'action');
        $grid->setStartIndex($page,$maxPerPage);
        $grid->setPath('app_user_region_list');
        $grid->setParentValue($userId);
        $grid->setCurrentObject($class);
        $grid->setIgnoredButtons(["more"]);
        $grid->setButtons();
   
        //Render the output
        return $this->render(
            'main/app.list.html.twig',array(
            'records'=>$dataGrid,
            'grid'=>$grid,
            'title'=>'List of Regions Assigned to User',
            'gridTemplate'=>'lists/base.list.html.twig'
        ));
    }


    /**
     * @Route("/administration/app-user-regions/{userId}/add", name="app_user_region_add")
     * @param Request $request
     * @param $userId
     * @return Response
     */
    public function newAction(Request $request,$userId)
    {

        $class = get_class($this);

        $this->denyAccessUnlessGranted('add',$class);

        $form = $this->createForm(AppUserRegionFormType::class);

        // only handles data on POST
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $userRegion = $form->getData();

            $em = $this->getDoctrine()->getManager();

            $user = $em->getRepository('AppBundle:AppUsers\User')
                ->find($userId);

            if(!($user instanceof User)) {

                $this->addFlash('error', 'You are trying to add a region to a user that does not exist');

                return $this->redirectToRoute('app_users_list');
            }

            $regions = $userRegion->getRegion();

            //Delete all programs under this given department before adding new departments
            $em->getRepository('AppBundle:AppUsers\UserRegion')
                ->deleteUserRegions($userId);

            foreach ($regions as $regionId)
            {
                $region = $em->getRepository('AppBundle:Location\Region')
                    ->find($regionId);

                $userRegion = new UserRegion();
                $userRegion->setUser($user);
                $userRegion->setRegion($region);
                $em->persist($userRegion);
                $em->flush();
            }

            $this->addFlash('success', 'Region(s) successfully linked to user');

            return $this->redirectToRoute('app_user_region_list',['userId'=>$userId]);
        }

        return $this->render(
            'main/app.form.html.twig',
            array(
                'formTemplate'=>'app.users/user.region',
                'form'=>$form->createView(),
                'title'=>'User Regions Details'
            )

        );
    }


    /**
     * @Route("/administration/app-user-regions/{userId}/delete/{userNo}", name="app_user_region_delete")
     * @param $userId
     * @param $userNo
     * @return Response
     * @internal param Request $request
     */
    public function deleteAction($userId,$userNo)
    {
        $class = get_class($this);

        $this->denyAccessUnlessGranted('delete',$class);

        $em = $this->getDoctrine()->getManager();

        $userRegion = $em->getRepository('AppBundle:AppUsers\UserRegion')->find($userNo);

        if($userRegion instanceof UserRegion)
        {
            $em->remove($userRegion);
            $em->flush();
            $this->addFlash('success', 'Region link successfully removed !');
        }
        else
        {
            $this->addFlash('error', 'Region link not found !');
        }

        return $this->redirectToRoute('app_user_region_list',['userId'=>$userId]);
    }

    
}