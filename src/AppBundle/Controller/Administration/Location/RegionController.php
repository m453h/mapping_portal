<?php

namespace AppBundle\Controller\Administration\Location;

use AppBundle\Entity\AppUsers\User;
use AppBundle\Entity\Location\Region;
use AppBundle\Form\Location\RegionFormType;
use Pagerfanta\Adapter\DoctrineDbalAdapter;
use Pagerfanta\Pagerfanta;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


class RegionController extends Controller
{

    /**
     * @Route("/administration/regions", name="region_list")
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

        $qb1 = $em->getRepository('AppBundle:Location\Region')
            ->findAllRegions($options);

        $qb2 = $em->getRepository('AppBundle:Location\Region')
            ->countAllRegions($qb1);

        $adapter =new DoctrineDbalAdapter($qb1,$qb2);
        $dataGrid = new Pagerfanta($adapter);
        $dataGrid->setMaxPerPage($maxPerPage);
        $dataGrid->setCurrentPage($page);
        $dataGrid->getCurrentPageResults();
        
        //Configure the grid
        $grid = $this->get('app.helper.grid_builder');
        $grid->addGridHeader('S/N',null,'index');
        $grid->addGridHeader('Name','name','text',true);
        $grid->addGridHeader('Actions',null,'action');
        $grid->setStartIndex($page,$maxPerPage);
        $grid->setPath('region_list');
        $grid->setCurrentObject($class);
        $grid->setButtons();
        
        //Render the output
        return $this->render(
            'administration/main/app.list.html.twig',array(
                'records'=>$dataGrid,
                'grid'=>$grid,
                'title'=>'Existing Regions',
                'gridTemplate'=>'administration/lists/location/region.list.html.twig'
        ));
    }

    /**
     * @Route("/administration/regions/add", name="region_add")
     * @param Request $request
     * @return Response
     */
    public function newAction(Request $request)
    {
        $class = get_class($this);
        
        $this->denyAccessUnlessGranted('add',$class);

        $form = $this->createForm(RegionFormType::class);

        // only handles data on POST
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $region = $form->getData();
            $em = $this->getDoctrine()->getManager();
            $em->persist($region);
            $em->flush();

            $this->addFlash('success','Region successfully created');

            return $this->redirectToRoute('region_list');
        }

        return $this->render(
            'administration/main/app.form.html.twig',
            array(
                'formTemplate'=>'location/region',
                'form'=>$form->createView(),
                'title'=>'Region Details',
            )

        );
    }


    /**
     * @Route("/administration/regions/edit/{regionId}", name="region_edit")
     * @param Request $request
     * @param Region $region
     * @return Response
     */
    public function editAction(Request $request,Region $region)
    {
        $class = get_class($this);

        $this->denyAccessUnlessGranted('edit',$class);

        $form = $this->createForm(RegionFormType::class,$region);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $region = $form->getData();

            $em = $this->getDoctrine()->getManager();
            $em->persist($region);
            $em->flush();

            $this->addFlash('success', 'Region successfully updated!');

            return $this->redirectToRoute('region_list');
        }

        return $this->render(
            'administration/main/app.form.html.twig',
            array(
                'formTemplate'=>'location/region',
                'form'=>$form->createView(),
                'title'=>'Region Details',
            )

        );
    }

    /**
     * @Route("/administration/regions/delete/{Id}", name="region_delete")
     * @param $Id
     * @return Response
     * @internal param Request $request
     */
    public function deleteAction($Id)
    {
        $class = get_class($this);
        
        $this->denyAccessUnlessGranted('delete',$class);

        $em = $this->getDoctrine()->getManager();

        $region = $em->getRepository('AppBundle:Location\Region')->find($Id);

        if($region instanceof Region)
        {
            $em->remove($region);
            $em->flush();
            $this->addFlash('success', 'Region successfully removed !');
        }
        else
        {
            $this->addFlash('error', 'Region not found !');
        }

        
        return $this->redirectToRoute('region_list');

    }
    
}