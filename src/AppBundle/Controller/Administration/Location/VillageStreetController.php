<?php

namespace AppBundle\Controller\Administration\Location;

use AppBundle\Entity\DataCollector\User;
use AppBundle\Entity\Location\Region;
use AppBundle\Entity\Location\VillageStreet;
use AppBundle\Entity\Location\Ward;
use AppBundle\Form\Location\RegionFormType;
use AppBundle\Form\Location\VillageStreetFormType;
use AppBundle\Form\Location\WardFormType;
use Pagerfanta\Adapter\DoctrineDbalAdapter;
use Pagerfanta\Pagerfanta;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


class VillageStreetController extends Controller
{

    /**
     * @Route("/administration/village-street", name="village_street_list")
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

        $qb1 = $em->getRepository('AppBundle:Location\VillageStreet')
            ->findAllAreas($options);

        $qb2 = $em->getRepository('AppBundle:Location\VillageStreet')
            ->countAllAreas($qb1);

        $adapter =new DoctrineDbalAdapter($qb1,$qb2);
        $dataGrid = new Pagerfanta($adapter);
        $dataGrid->setMaxPerPage($maxPerPage);
        $dataGrid->setCurrentPage($page);
        $dataGrid->getCurrentPageResults();
        
        //Configure the grid
        $grid = $this->get('app.helper.grid_builder');
        $grid->addGridHeader('S/N',null,'index');
        $grid->addGridHeader('Name','name','text',true);
        $grid->addGridHeader('Ward',null,'text',false);
        $grid->addGridHeader('District',null,'text',false);
        $grid->addGridHeader('Region',null,'text',false);
        $grid->addGridHeader('Actions',null,'action');
        $grid->setStartIndex($page,$maxPerPage);
        $grid->setPath('village_street_list');
        $grid->setCurrentObject($class);
        $grid->setButtons();
        
        //Render the output
        return $this->render(
            'administration/main/app.list.html.twig',array(
                'records'=>$dataGrid,
                'grid'=>$grid,
                'title'=>'Existing Villages/Streets',
                'gridTemplate'=>'lists/base.list.html.twig'
        ));
    }

    /**
     * @Route("/administration/village-street/add", name="village_street_add")
     * @param Request $request
     * @return Response
     */
    public function newAction(Request $request)
    {
        $class = get_class($this);
        
        $this->denyAccessUnlessGranted('add',$class);

        $form = $this->createForm(VillageStreetFormType::class);

        // only handles data on POST
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $ward = $form->getData();
            $em = $this->getDoctrine()->getManager();
            $em->persist($ward);
            $em->flush();

            $this->addFlash('success','Village street successfully created');

            return $this->redirectToRoute('village_street_list');
        }

        return $this->render(
            'administration/main/app.form.html.twig',
            array(
                'formTemplate'=>'location/village.street',
                'form'=>$form->createView(),
                'title'=>'Village Details',
            )

        );
    }


    /**
     * @Route("/administration/village-street/edit/{areaId}", name="village_street_edit")
     * @param Request $request
     * @param VillageStreet $villageStreet
     * @return Response
     */
    public function editAction(Request $request,VillageStreet $villageStreet)
    {
        $class = get_class($this);

        $this->denyAccessUnlessGranted('edit',$class);

        $form = $this->createForm(VillageStreetFormType::class,$villageStreet);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $villageStreet = $form->getData();

            $em = $this->getDoctrine()->getManager();
            $em->persist($villageStreet);
            $em->flush();

            $this->addFlash('success', 'Village/Street successfully updated!');

            return $this->redirectToRoute('village_street_list');
        }

        return $this->render(
            'administration/main/app.form.html.twig',
            array(
                'formTemplate'=>'location/village.street',
                'form'=>$form->createView(),
                'title'=>'Village/Street Details',
            )

        );
    }

    /**
     * @Route("/administration/village-street/delete/{Id}", name="village_street_delete")
     * @param $Id
     * @return Response
     * @internal param Request $request
     */
    public function deleteAction($Id)
    {
        $class = get_class($this);
        
        $this->denyAccessUnlessGranted('delete',$class);

        $em = $this->getDoctrine()->getManager();

        $villageStreet = $em->getRepository('AppBundle:Location\VillageStreet')->find($Id);

        if($villageStreet instanceof VillageStreet)
        {
            $em->remove($villageStreet);
            $em->flush();
            $this->addFlash('success', 'Village/Street successfully removed !');
        }
        else
        {
            $this->addFlash('error', 'Village/Street not found !');
        }

        
        return $this->redirectToRoute('village_street_list');

    }
    
}