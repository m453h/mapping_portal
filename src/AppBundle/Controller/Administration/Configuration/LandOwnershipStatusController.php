<?php

namespace AppBundle\Controller\Administration\Configuration;

use AppBundle\Entity\Configuration\LandOwnerShipStatus;
use AppBundle\Entity\Configuration\Zone;
use AppBundle\Form\Configuration\LandOwnerShipStatusFormType;
use AppBundle\Form\Configuration\ZoneFormType;
use Pagerfanta\Adapter\DoctrineDbalAdapter;
use Pagerfanta\Pagerfanta;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


class LandOwnershipStatusController extends Controller
{

    /**
     * @Route("/administration/land-ownership-status", name="land_ownership_status_list")
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

        $qb1 = $em->getRepository('AppBundle:Configuration\LandOwnerShipStatus')
            ->findAllLandOwnerShipStatus($options);

        $qb2 = $em->getRepository('AppBundle:Configuration\LandOwnerShipStatus')
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
        $grid->setPath('land_ownership_status_list');
        $grid->setCurrentObject($class);
        $grid->setButtons();
        
        //Render the output
        return $this->render(
            'administration/main/app.list.html.twig',array(
                'records'=>$dataGrid,
                'grid'=>$grid,
                'title'=>'Existing Land Ownership Status',
                'gridTemplate'=>'administration/lists/base.list.html.twig'
        ));
    }

    /**
     * @Route("/administration/land-ownership-status/add", name="land_ownership_status_add")
     * @param Request $request
     * @return Response
     */
    public function newAction(Request $request)
    {
        $class = get_class($this);
        
        $this->denyAccessUnlessGranted('add',$class);

        $form = $this->createForm(LandOwnerShipStatusFormType::class);

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
                ->logUserAction('CONFIGURATION\LAND_OWNERSHIP_STATUS','EDIT',null,$data);

            return $this->redirectToRoute('land_ownership_status_list');
        }

        return $this->render(
            'administration/main/app.form.html.twig',
            array(
                'formTemplate'=>'configuration/land.ownership.status',
                'form'=>$form->createView(),
                'title'=>'Land Ownership Status Details',
            )

        );
    }


    /**
     * @Route("/administration/land-ownership-status/edit/{statusId}", name="land_ownership_status_edit")
     * @param Request $request
     * @param LandOwnerShipStatus $status
     * @return Response
     */
    public function editAction(Request $request,LandOwnerShipStatus $status)
    {
        $class = get_class($this);

        $this->denyAccessUnlessGranted('edit',$class);

        $form = $this->createForm(LandOwnerShipStatusFormType::class,$status);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $data = $form->getData();

            $em = $this->getDoctrine()->getManager();
            $em->persist($data);
            $em->flush();

            $this->addFlash('success', 'Status successfully updated!');

            $this->get('app.helper.audit_trail_logger')
                ->logUserAction('CONFIGURATION\LAND_OWNERSHIP_STATUS','EDIT',$status,$data);

            return $this->redirectToRoute('land_ownership_status_list');
        }

        return $this->render(
            'administration/main/app.form.html.twig',
            array(
                'formTemplate'=>'configuration/land.ownership.status',
                'form'=>$form->createView(),
                'title'=>'Land Ownership Status Details',
            )

        );
    }

    /**
     * @Route("/administration/land-ownership-status/delete/{Id}", name="land_ownership_status_delete")
     * @param $Id
     * @return Response
     * @internal param Request $request
     */
    public function deleteAction($Id)
    {
        $class = get_class($this);
        
        $this->denyAccessUnlessGranted('delete',$class);

        $em = $this->getDoctrine()->getManager();

        $data = $em->getRepository('AppBundle:Configuration\LandOwnerShipStatus')->find($Id);

        if($data instanceof LandOwnerShipStatus)
        {
            $em->remove($data);
            $em->flush();
            $this->addFlash('success', 'Status successfully removed !');

            $this->get('app.helper.audit_trail_logger')
                ->logUserAction('CONFIGURATION\LAND_OWNERSHIP_STATUS','DELETE',$data,null);
        }
        else
        {
            $this->addFlash('error', 'Status not found !');
        }

        
        return $this->redirectToRoute('land_ownership_status_list');

    }
    
}