<?php

namespace AppBundle\Controller\Administration\Location;

use AppBundle\Entity\DataCollector\User;
use AppBundle\Entity\Location\Region;
use AppBundle\Entity\Location\Ward;
use AppBundle\Form\Location\RegionFormType;
use AppBundle\Form\Location\WardFormType;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Pagerfanta\Adapter\DoctrineDbalAdapter;
use Pagerfanta\Pagerfanta;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


class WardController extends Controller
{

    /**
     * @Route("/administration/wards", name="ward_list")
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

        $qb1 = $em->getRepository('AppBundle:Location\Ward')
            ->findAllWards($options);

        $qb2 = $em->getRepository('AppBundle:Location\Ward')
            ->countAllWards($qb1);

        $adapter =new DoctrineDbalAdapter($qb1,$qb2);
        $dataGrid = new Pagerfanta($adapter);
        $dataGrid->setMaxPerPage($maxPerPage);
        $dataGrid->setCurrentPage($page);
        $dataGrid->getCurrentPageResults();
        
        //Configure the grid
        $grid = $this->get('app.helper.grid_builder');
        $grid->addGridHeader('S/N',null,'index');
        $grid->addGridHeader('Name','name','text',true);
        $grid->addGridHeader('District',null,'text',false);
        $grid->addGridHeader('Region',null,'text',false);
        $grid->addGridHeader('Actions',null,'action');
        $grid->setStartIndex($page,$maxPerPage);
        $grid->setPath('ward_list');
        $grid->setCurrentObject($class);
        $grid->setButtons();
        
        //Render the output
        return $this->render(
            'administration/main/app.list.html.twig',array(
                'records'=>$dataGrid,
                'grid'=>$grid,
                'title'=>'Existing Wards',
                'gridTemplate'=>'administration/lists/base.list.html.twig'
        ));
    }

    /**
     * @Route("/administration/wards/add", name="ward_add")
     * @param Request $request
     * @return Response
     */
    public function newAction(Request $request)
    {
        $class = get_class($this);
        
        $this->denyAccessUnlessGranted('add',$class);

        $form = $this->createForm(WardFormType::class);

        // only handles data on POST
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $ward = $form->getData();
            $em = $this->getDoctrine()->getManager();
            $em->persist($ward);
            $em->flush();

            $this->addFlash('success','Ward successfully created');

            $this->get('app.helper.audit_trail_logger')
                ->logUserAction('LOCATION\WARD','ADD',null,$ward);

            return $this->redirectToRoute('ward_list');
        }

        return $this->render(
            'administration/main/app.form.html.twig',
            array(
                'formTemplate'=>'location/ward',
                'form'=>$form->createView(),
                'title'=>'Ward Details',
            )

        );
    }


    /**
     * @Route("/administration/wards/edit/{wardId}", name="ward_edit")
     * @param Request $request
     * @param Ward $ward
     * @return Response
     */
    public function editAction(Request $request,Ward $ward)
    {
        $class = get_class($this);

        $this->denyAccessUnlessGranted('edit',$class);

        $form = $this->createForm(WardFormType::class,$ward);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $data = $form->getData();

            $em = $this->getDoctrine()->getManager();
            $em->persist($data);
            $em->flush();

            $this->addFlash('success', 'Ward successfully updated!');

            $this->get('app.helper.audit_trail_logger')
                ->logUserAction('LOCATION\WARD','EDIT',$ward,$data);


            return $this->redirectToRoute('ward_list');
        }

        return $this->render(
            'administration/main/app.form.html.twig',
            array(
                'formTemplate'=>'location/ward',
                'form'=>$form->createView(),
                'title'=>'Ward Details',
            )

        );
    }

    /**
     * @Route("/administration/wards/delete/{Id}", name="ward_delete")
     * @param $Id
     * @return Response
     * @internal param Request $request
     */
    public function deleteAction($Id)
    {
        $class = get_class($this);
        
        $this->denyAccessUnlessGranted('delete',$class);

        $em = $this->getDoctrine()->getManager();

        $ward = $em->getRepository('AppBundle:Location\Ward')->find($Id);

        if($ward instanceof Ward)
        {
            try {
                $em->remove($ward);
                $em->flush();
                $this->addFlash('success', 'Ward successfully removed !');

                $this->get('app.helper.audit_trail_logger')
                    ->logUserAction('LOCATION\WARD','DELETE',$ward,null);
            }
            catch (ForeignKeyConstraintViolationException $e)
            {
                $this->addFlash('warning', 'Ward can not be removed, make sure there are no child data elements that depend on this record !');
            }
        }
        else
        {
            $this->addFlash('error', 'Ward not found !');
        }

        
        return $this->redirectToRoute('ward_list');

    }



    /**
     * @Route("/administration/api/getWards",options={"expose"=true}, name="api_get_wards")
     */
    public function getModulesAction()
    {
        $em = $this->getDoctrine()->getManager();

        $value = $this->get('request_stack')->getCurrentRequest()->get('value');

        $data = $em->getRepository('AppBundle:Location\Ward')
            ->findWardsByDistrict($value);

        return new JsonResponse($data);
    }
    
}