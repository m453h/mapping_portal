<?php

namespace AppBundle\Controller\Configuration;

use AppBundle\Entity\Configuration\CourtBuildingOwnershipStatus;
use AppBundle\Entity\Configuration\CourtBuildingStatus;
use AppBundle\Entity\Configuration\CourtCategory;
use AppBundle\Entity\Configuration\CourtLevel;
use AppBundle\Entity\Configuration\LandOwnerShipStatus;
use AppBundle\Entity\Configuration\Zone;
use AppBundle\Form\Configuration\CourtBuildingOwnershipStatusFormType;
use AppBundle\Form\Configuration\CourtBuildingStatusFormType;
use AppBundle\Form\Configuration\CourtCategoryFormType;
use AppBundle\Form\Configuration\CourtLevelFormType;
use AppBundle\Form\Configuration\LandOwnerShipStatusFormType;
use AppBundle\Form\Configuration\ZoneFormType;
use Pagerfanta\Adapter\DoctrineDbalAdapter;
use Pagerfanta\Pagerfanta;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


class CourtLevelController extends Controller
{

    /**
     * @Route("/court-level", name="court_level_list")
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

        $qb1 = $em->getRepository('AppBundle:Configuration\CourtLevel')
            ->findAllCourtLevels($options);

        $qb2 = $em->getRepository('AppBundle:Configuration\CourtLevel')
            ->countAllCourtLevels($qb1);

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
        $grid->setPath('court_level_list');
        $grid->setCurrentObject($class);
        $grid->setButtons();
        
        //Render the output
        return $this->render(
            'main/app.list.html.twig',array(
                'records'=>$dataGrid,
                'grid'=>$grid,
                'title'=>'Existing Court Levels',
                'gridTemplate'=>'lists/base.list.html.twig'
        ));
    }

    /**
     * @Route("/court-level/add", name="court_level_add")
     * @param Request $request
     * @return Response
     */
    public function newAction(Request $request)
    {
        $class = get_class($this);
        
        $this->denyAccessUnlessGranted('add',$class);

        $form = $this->createForm(CourtLevelFormType::class);

        // only handles data on POST
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $data = $form->getData();
            $em = $this->getDoctrine()->getManager();
            $em->persist($data);
            $em->flush();

            $this->addFlash('success','Court Level successfully created');

            return $this->redirectToRoute('court_level_list');
        }

        return $this->render(
            'main/app.form.html.twig',
            array(
                'formTemplate'=>'configuration/court.level',
                'form'=>$form->createView(),
                'title'=>'Court Level Details',
            )

        );
    }


    /**
     * @Route("/court-category/edit/{categoryId}", name="court_level_edit")
     * @param Request $request
     * @param CourtLevel $level
     * @return Response
     */
    public function editAction(Request $request,CourtLevel $level)
    {
        $class = get_class($this);

        $this->denyAccessUnlessGranted('edit',$class);

        $form = $this->createForm(CourtLevelFormType::class,$level);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $data = $form->getData();

            $em = $this->getDoctrine()->getManager();
            $em->persist($data);
            $em->flush();

            $this->addFlash('success', 'Court level successfully updated!');

            return $this->redirectToRoute('court_level_list');
        }

        return $this->render(
            'main/app.form.html.twig',
            array(
                'formTemplate'=>'configuration/court.level',
                'form'=>$form->createView(),
                'title'=>'Court Level Details',
            )

        );
    }

    /**
     * @Route("/court-level/delete/{Id}", name="court_level_delete")
     * @param $Id
     * @return Response
     * @internal param Request $request
     */
    public function deleteAction($Id)
    {
        $class = get_class($this);
        
        $this->denyAccessUnlessGranted('delete',$class);

        $em = $this->getDoctrine()->getManager();

        $data = $em->getRepository('AppBundle:Configuration\CourtLevel')->find($Id);

        if($data instanceof CourtLevel)
        {
            $em->remove($data);
            $em->flush();
            $this->addFlash('success', 'Court level successfully removed !');
        }
        else
        {
            $this->addFlash('error', 'Category not found !');
        }

        
        return $this->redirectToRoute('court_level_list');

    }
    
}