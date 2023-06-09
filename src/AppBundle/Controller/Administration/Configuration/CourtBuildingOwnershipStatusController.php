<?php

namespace AppBundle\Controller\Administration\Configuration;

use AppBundle\Entity\Configuration\CourtBuildingOwnershipStatus;
use AppBundle\Entity\Configuration\LandOwnerShipStatus;
use AppBundle\Entity\Configuration\Zone;
use AppBundle\Form\Configuration\CourtBuildingOwnershipStatusFormType;
use AppBundle\Form\Configuration\LandOwnerShipStatusFormType;
use AppBundle\Form\Configuration\ZoneFormType;
use Pagerfanta\Adapter\DoctrineDbalAdapter;
use Pagerfanta\Pagerfanta;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


class CourtBuildingOwnershipStatusController extends Controller
{

    /**
     * @Route("/administration/court-building-ownership-status", name="court_building_ownership_status_list")
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
            'administration/main/app.list.html.twig',array(
                'records'=>$dataGrid,
                'grid'=>$grid,
                'title'=>'Existing Court Building Ownership Status',
                'gridTemplate'=>'administration/lists/base.list.html.twig'
        ));
    }

    /**
     * @Route("/administration/court-building-ownership-status/add", name="court_building_ownership_status_add")
     * @param Request $request
     * @return Response
     */
    public function newAction(Request $request)
    {
        $class = get_class($this);
        
        $this->denyAccessUnlessGranted('add',$class);

        $form = $this->createForm(CourtBuildingOwnershipStatusFormType::class);

        // only handles data on POST
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $data = $form->getData();
            $em = $this->getDoctrine()->getManager();
            $em->persist($data);
            $em->flush();

            $this->addFlash('success','Status successfully created');

            $this->get('app.helper.audit_trail_logger')
                ->logUserAction('CONFIGURATION\COURT_BUILDING_OWNERSHIP_STATUS','ADD',null,$data);

            return $this->redirectToRoute('court_building_ownership_status_list');
        }

        return $this->render(
            'administration/main/app.form.html.twig',
            array(
                'formTemplate'=>'configuration/court.building.ownership.status',
                'form'=>$form->createView(),
                'title'=>'Court Building Ownership Status Details',
            )

        );
    }


    /**
     * @Route("/administration/court-building-ownership-status/edit/{statusId}", name="court_building_ownership_status_edit")
     * @param Request $request
     * @param CourtBuildingOwnershipStatus $status
     * @return Response
     */
    public function editAction(Request $request,CourtBuildingOwnershipStatus $status)
    {
        $class = get_class($this);

        $this->denyAccessUnlessGranted('edit',$class);

        $form = $this->createForm(CourtBuildingOwnershipStatusFormType::class,$status);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $data = $form->getData();

            $em = $this->getDoctrine()->getManager();
            $em->persist($data);
            $em->flush();

            $this->addFlash('success', 'Status successfully updated!');

            $this->get('app.helper.audit_trail_logger')
                ->logUserAction('CONFIGURATION\COURT_BUILDING_OWNERSHIP_STATUS','EDIT',$status,$data);

            return $this->redirectToRoute('court_building_ownership_status_list');
        }

        return $this->render(
            'administration/main/app.form.html.twig',
            array(
                'formTemplate'=>'configuration/court.building.ownership.status',
                'form'=>$form->createView(),
                'title'=>'Court Building Ownership Status Details',
            )

        );
    }

    /**
     * @Route("/administration/court-building-ownership-status/delete/{Id}", name="court_building_ownership_status_delete")
     * @param $Id
     * @return Response
     * @internal param Request $request
     */
    public function deleteAction($Id)
    {
        $class = get_class($this);
        
        $this->denyAccessUnlessGranted('delete',$class);

        $em = $this->getDoctrine()->getManager();

        $data = $em->getRepository('AppBundle:Configuration\CourtBuildingOwnershipStatus')->find($Id);

        if($data instanceof CourtBuildingOwnershipStatus)
        {
            $em->remove($data);
            $em->flush();
            $this->addFlash('success', 'Status successfully removed !');

            $this->get('app.helper.audit_trail_logger')
                ->logUserAction('CONFIGURATION\COURT_BUILDING_OWNERSHIP_STATUS','DELETE',$data,null);
        }
        else
        {
            $this->addFlash('error', 'Status not found !');
        }

        
        return $this->redirectToRoute('court_building_ownership_status_list');

    }
    
}