<?php

namespace AppBundle\Controller\Administration\Configuration;

use AppBundle\Entity\Configuration\CourtBuildingOwnershipStatus;
use AppBundle\Entity\Configuration\CourtBuildingStatus;
use AppBundle\Entity\Configuration\CourtEnvironmentalStatus;
use AppBundle\Entity\Configuration\LandOwnerShipStatus;
use AppBundle\Entity\Configuration\Zone;
use AppBundle\Form\Configuration\CourtBuildingOwnershipStatusFormType;
use AppBundle\Form\Configuration\CourtBuildingStatusFormType;
use AppBundle\Form\Configuration\CourtEnvironmentalStatusFormType;
use AppBundle\Form\Configuration\LandOwnerShipStatusFormType;
use AppBundle\Form\Configuration\ZoneFormType;
use Pagerfanta\Adapter\DoctrineDbalAdapter;
use Pagerfanta\Pagerfanta;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


class CourtEnvironmentalStatusController extends Controller
{

    /**
     * @Route("/administration/court-environmental-status", name="court_environmental_status_list")
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

        $qb1 = $em->getRepository('AppBundle:Configuration\CourtEnvironmentalStatus')
            ->findAllCourtEnvironmentalStatus($options);

        $qb2 = $em->getRepository('AppBundle:Configuration\CourtEnvironmentalStatus')
            ->countAllCourtEnvironmentalStatus($qb1);

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
        $grid->setPath('court_environmental_status_list');
        $grid->setCurrentObject($class);
        $grid->setButtons();
        
        //Render the output
        return $this->render(
            'main/app.list.html.twig',array(
                'records'=>$dataGrid,
                'grid'=>$grid,
                'title'=>'Existing Court Environmental Status',
                'gridTemplate'=>'lists/base.list.html.twig'
        ));
    }

    /**
     * @Route("/administration/court-environmental-status/add", name="court_environmental_status_add")
     * @param Request $request
     * @return Response
     */
    public function newAction(Request $request)
    {
        $class = get_class($this);
        
        $this->denyAccessUnlessGranted('add',$class);

        $form = $this->createForm(CourtBuildingStatusFormType::class);

        // only handles data on POST
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $data = $form->getData();
            $em = $this->getDoctrine()->getManager();
            $em->persist($data);
            $em->flush();

            $this->addFlash('success','Status successfully created');

            return $this->redirectToRoute('court_building_status_list');
        }

        return $this->render(
            'main/app.form.html.twig',
            array(
                'formTemplate'=>'configuration/court.environmental.status',
                'form'=>$form->createView(),
                'title'=>'Court Environmental Status Details',
            )

        );
    }


    /**
     * @Route("/administration/court-environmental-status/edit/{statusId}", name="court_environmental_status_edit")
     * @param Request $request
     * @param CourtEnvironmentalStatus $status
     * @return Response
     */
    public function editAction(Request $request,CourtEnvironmentalStatus $status)
    {
        $class = get_class($this);

        $this->denyAccessUnlessGranted('edit',$class);

        $form = $this->createForm(CourtEnvironmentalStatusFormType::class,$status);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $data = $form->getData();

            $em = $this->getDoctrine()->getManager();
            $em->persist($data);
            $em->flush();

            $this->addFlash('success', 'Status successfully updated!');

            return $this->redirectToRoute('court_environmental_status_list');
        }

        return $this->render(
            'main/app.form.html.twig',
            array(
                'formTemplate'=>'configuration/court.environmental.status',
                'form'=>$form->createView(),
                'title'=>'Court Environmental Status Details',
            )

        );
    }

    /**
     * @Route("/administration/court-environmental-status/delete/{Id}", name="court_environmental_status_delete")
     * @param $Id
     * @return Response
     * @internal param Request $request
     */
    public function deleteAction($Id)
    {
        $class = get_class($this);
        
        $this->denyAccessUnlessGranted('delete',$class);

        $em = $this->getDoctrine()->getManager();

        $data = $em->getRepository('AppBundle:Configuration\CourtEnvironmentalStatus')->find($Id);

        if($data instanceof CourtEnvironmentalStatus)
        {
            $em->remove($data);
            $em->flush();
            $this->addFlash('success', 'Status successfully removed !');
        }
        else
        {
            $this->addFlash('error', 'Status not found !');
        }

        
        return $this->redirectToRoute('court_environmental_status_list');

    }
    
}