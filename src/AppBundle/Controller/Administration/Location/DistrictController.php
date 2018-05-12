<?php

namespace AppBundle\Controller\Administration\Location;

use AppBundle\Entity\AppUsers\User;
use AppBundle\Entity\Location\District;
use AppBundle\Entity\Location\Region;
use AppBundle\Form\Location\DistrictFormType;
use AppBundle\Form\Location\RegionFormType;
use Pagerfanta\Adapter\DoctrineDbalAdapter;
use Pagerfanta\Pagerfanta;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


class DistrictController extends Controller
{

    /**
     * @Route("/administration/districts", name="district_list")
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

        $qb1 = $em->getRepository('AppBundle:Location\District')
            ->findAllDistricts($options);

        $qb2 = $em->getRepository('AppBundle:Location\District')
            ->countAllDistricts($qb1);

        $adapter =new DoctrineDbalAdapter($qb1,$qb2);
        $dataGrid = new Pagerfanta($adapter);
        $dataGrid->setMaxPerPage($maxPerPage);
        $dataGrid->setCurrentPage($page);
        $dataGrid->getCurrentPageResults();
        
        //Configure the grid
        $grid = $this->get('app.helper.grid_builder');
        $grid->addGridHeader('S/N',null,'index');
        $grid->addGridHeader('Name','name','text',true);
        $grid->addGridHeader('Region',null,'text',false);
        $grid->addGridHeader('Actions',null,'action');
        $grid->setStartIndex($page,$maxPerPage);
        $grid->setPath('district_list');
        $grid->setCurrentObject($class);
        $grid->setButtons();
        
        //Render the output
        return $this->render(
            'administration/main/app.list.html.twig',array(
                'records'=>$dataGrid,
                'grid'=>$grid,
                'title'=>'Existing Districts',
                'gridTemplate'=>'administration/lists/base.list.html.twig'
        ));
    }

    /**
     * @Route("/administration/districts/add", name="district_add")
     * @param Request $request
     * @return Response
     */
    public function newAction(Request $request)
    {
        $class = get_class($this);
        
        $this->denyAccessUnlessGranted('add',$class);

        $form = $this->createForm(DistrictFormType::class);

        // only handles data on POST
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $district = $form->getData();
            $em = $this->getDoctrine()->getManager();
            $em->persist($district);
            $em->flush();

            $this->addFlash('success','District successfully created');

            return $this->redirectToRoute('district_list');
        }

        return $this->render(
            'administration/main/app.form.html.twig',
            array(
                'formTemplate'=>'location/district',
                'form'=>$form->createView(),
                'title'=>'District Details',
            )

        );
    }


    /**
     * @Route("/administration/districts/edit/{districtId}", name="district_edit")
     * @param Request $request
     * @return Response
     */
    public function editAction(Request $request,District $district)
    {
        $class = get_class($this);

        $this->denyAccessUnlessGranted('edit',$class);

        $form = $this->createForm(DistrictFormType::class,$district);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $district = $form->getData();

            $em = $this->getDoctrine()->getManager();
            $em->persist($district);
            $em->flush();

            $this->addFlash('success', 'District successfully updated!');

            return $this->redirectToRoute('district_list');
        }

        return $this->render(
            'administration/main/app.form.html.twig',
            array(
                'formTemplate'=>'location/district',
                'form'=>$form->createView(),
                'title'=>'District Details',
            )

        );
    }

    /**
     * @Route("/administration/districts/delete/{districtId}", name="district_delete")
     * @param $districtId
     * @return Response
     * @internal param Request $request
     */
    public function deleteAction($districtId)
    {
        $class = get_class($this);
        
        $this->denyAccessUnlessGranted('delete',$class);

        $em = $this->getDoctrine()->getManager();

        $district = $em->getRepository('AppBundle:Location\District')->find($districtId);

        if($district instanceof District)
        {
            $em->remove($district);
            $em->flush();
            $this->addFlash('success', 'District successfully removed !');
        }
        else
        {
            $this->addFlash('error', 'District not found !');
        }

        
        return $this->redirectToRoute('district_list');

    }


    /**
     * @Route("/administration/api/getDistricts",options={"expose"=true}, name="api_get_districts")
     */
    public function getModulesAction()
    {
        $em = $this->getDoctrine()->getManager();

        $value = $this->get('request_stack')->getCurrentRequest()->get('value');

        $data = $em->getRepository('AppBundle:Location\District')
            ->findDistrictsByRegion($value);

        return new JsonResponse($data);
    }
    
}