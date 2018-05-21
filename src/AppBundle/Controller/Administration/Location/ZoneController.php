<?php

namespace AppBundle\Controller\Administration\Location;

use AppBundle\Entity\Location\Zone;
use AppBundle\Form\Location\ZoneFormType;
use Pagerfanta\Adapter\DoctrineDbalAdapter;
use Pagerfanta\Pagerfanta;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


class ZoneController extends Controller
{

    /**
     * @Route("/administration/zones", name="zone_list")
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

        $qb1 = $em->getRepository('AppBundle:Location\Zone')
            ->findAllZones($options);

        $qb2 = $em->getRepository('AppBundle:Location\Zone')
            ->countAllZones($qb1);

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
        $grid->setPath('zone_list');
        $grid->setCurrentObject($class);
        $grid->setButtons();
        
        //Render the output
        return $this->render(
            'administration/main/app.list.html.twig',array(
                'records'=>$dataGrid,
                'grid'=>$grid,
                'title'=>'Existing Zones',
                'gridTemplate'=>'administration/lists/base.list.html.twig'
        ));
    }

    /**
     * @Route("/administration/zones/add", name="zone_add")
     * @param Request $request
     * @return Response
     */
    public function newAction(Request $request)
    {
        $class = get_class($this);
        
        $this->denyAccessUnlessGranted('add',$class);

        $form = $this->createForm(ZoneFormType::class);

        // only handles data on POST
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $data = $form->getData();
            $em = $this->getDoctrine()->getManager();
            $em->persist($data);
            $em->flush();

            $this->addFlash('success','Zone successfully created');

            return $this->redirectToRoute('zone_list');
        }

        return $this->render(
            'administration/main/app.form.html.twig',
            array(
                'formTemplate'=>'configuration/zone',
                'form'=>$form->createView(),
                'title'=>'Zone Details',
            )

        );
    }


    /**
     * @Route("/administration/zones/edit/{zoneId}", name="zone_edit")
     * @param Request $request
     * @param Zone $zone
     * @return Response
     */
    public function editAction(Request $request,Zone $zone)
    {
        $class = get_class($this);

        $this->denyAccessUnlessGranted('edit',$class);

        $form = $this->createForm(ZoneFormType::class,$zone);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $data = $form->getData();

            $em = $this->getDoctrine()->getManager();
            $em->persist($data);
            $em->flush();

            $this->addFlash('success', 'Zone successfully updated!');

            return $this->redirectToRoute('zone_list');
        }

        return $this->render(
            'administration/main/app.form.html.twig',
            array(
                'formTemplate'=>'configuration/zone',
                'form'=>$form->createView(),
                'title'=>'Zone Details',
            )

        );
    }

    /**
     * @Route("/administration/zones/delete/{Id}", name="zone_delete")
     * @param $Id
     * @return Response
     * @internal param Request $request
     */
    public function deleteAction($Id)
    {
        $class = get_class($this);
        
        $this->denyAccessUnlessGranted('delete',$class);

        $em = $this->getDoctrine()->getManager();

        $data = $em->getRepository('AppBundle:Location\Zone')->find($Id);

        if($data instanceof Zone)
        {
            $em->remove($data);
            $em->flush();
            $this->addFlash('success', 'Zone successfully removed !');
        }
        else
        {
            $this->addFlash('error', 'Zone not found !');
        }

        
        return $this->redirectToRoute('zone_list');

    }
    
}