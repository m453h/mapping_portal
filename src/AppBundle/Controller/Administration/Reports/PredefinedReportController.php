<?php

namespace AppBundle\Controller\Administration\Reports;

use AppBundle\Form\Reports\PredefinedReportFormType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


class PredefinedReportController extends Controller
{


    /**
     * @Route("/administration/pre-defined-report", name="pre_defined_report_builder")
     * @param Request $request
     * @return Response
     */
    public function reportAction(Request $request)
    {

        $form = $this->createForm(PredefinedReportFormType::class);

        // only handles data on POST
        $form->handleRequest($request);
       
        if ($form->isSubmitted() && $form->isValid())
        {
            $report = $form['report']->getData();

            $fileName = '_COURT_MAPPING_REPORT';

            $em = $this->getDoctrine()->getManager();

            $verificationStatus = $form['status']->getData();
            
            if($report=='1')
            {
                $title = ' REPORT ON COURTS PER CATEGORY';

                if($verificationStatus=='1')
                {
                    $title = $title.' (VERIFIED DATA)';
                }
                
                $data = $em->getRepository('AppBundle:Court\Court')
                    ->findCourtTotalPerCategory($verificationStatus);

                $grid = $this->get('app.helper.grid_builder');
                $grid->addGridHeader('S/N',null,'index');
                $grid->addGridHeader('Court Level',null,null,false);
                $grid->addGridHeader('Total',null,null,false);

                $gridTemplate = 'lists/reports/court.category.list.html.twig';

                $mainTemplate = 'main/app.report.list.html.twig';

                $regionTotals = null;

                $districtTotals = null;
            }
            else if($report=='2')
            {

                $title = ' REPORT ON COURTS PER REGION PER DISTRICT AND WARD';

                if($verificationStatus=='1')
                {
                    $title = $title.' (VERIFIED DATA)';
                }
                
                $data = $em->getRepository('AppBundle:Court\Court')
                    ->findCourtTotalPerRegionPerWard(false,$verificationStatus);

                $regionTotals = $em->getRepository('AppBundle:Court\Court')
                    ->findCourtTotalPerRegion(false,$verificationStatus);

                $districtTotals = $em->getRepository('AppBundle:Court\Court')
                    ->findCourtTotalDistricts(false,$verificationStatus);
              
                $grid = $this->get('app.helper.grid_builder');
                $grid->addGridHeader('S/N',null,'index');
                $grid->addGridHeader('Region',null,null,false);
                $grid->addGridHeader('District',null,null,false);
                $grid->addGridHeader('Ward',null,null,false);
                $grid->addGridHeader('Total',null,null,false);

                $gridTemplate = null;

                $mainTemplate = 'lists/reports/app.court.per.ward.list.html.twig';

            }
            else if($report=='3')
            {

                $title = ' REPORT ON REGIONS FULLY COVERED';

                if($verificationStatus=='1')
                {
                    $title = $title.' (VERIFIED DATA)';
                }

                $data = $em->getRepository('AppBundle:Court\Court')
                    ->findCourtTotalPerRegionPerWard(true,$verificationStatus);

                $regionTotals = $em->getRepository('AppBundle:Court\Court')
                    ->findCourtTotalPerRegion(true,$verificationStatus);

                $districtTotals = $em->getRepository('AppBundle:Court\Court')
                    ->findCourtTotalDistricts(true,$verificationStatus);

                $grid = $this->get('app.helper.grid_builder');
                $grid->addGridHeader('S/N',null,'index');
                $grid->addGridHeader('Region',null,null,false);
                $grid->addGridHeader('District',null,null,false);
                $grid->addGridHeader('Ward',null,null,false);
                $grid->addGridHeader('Total',null,null,false);

                $gridTemplate = null;

                $mainTemplate = 'main/app.report.list.html.twig';
                $gridTemplate = 'lists/base.list.html.twig';

            }


            $summary = array(
                $title
            );

            $data = array(
                'records'=>$data,
                'grid'=>$grid,
                'summary'=>$summary,
                'gridTemplate'=>$gridTemplate,
                'regionTotals'=>$regionTotals,
                'districtTotals'=>$districtTotals
            );


            if($form->get('pdf')->isClicked())
            {
                $html = $this->renderView($mainTemplate,$data);

                return new Response(
                    $this->get('knp_snappy.pdf')->getOutputFromHtml($html),
                    200,
                    array(
                        'Content-Type'          => 'application/pdf',
                        'Content-Disposition'   => 'attachment; filename="'.$fileName.'.pdf"'
                    )
                );
            }
            else if($form->get('preview')->isClicked())
            {
                return $this->render($mainTemplate,$data);
            }

        }

        return $this->render(
            'main/app.form.html.twig',
            array(
                'formTemplate'=>'reports/predefined.report',
                'form'=>$form->createView(),
                'title'=>'Pre defined Report Builder'
            )

        );



    }



}