<?php

namespace AppBundle\Controller\Reports;

use AppBundle\Form\Reports\PredefinedReportFormType;
use AppBundle\Form\Reports\VisualReportFormType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


class VisualReportController extends Controller
{


    /**
     * @Route("/visual-report-builder", name="visual_report_builder")
     * @param Request $request
     * @return Response
     */
    public function reportAction(Request $request)
    {

        $form = $this->createForm(VisualReportFormType::class);

        // only handles data on POST
        $form->handleRequest($request);
       
        if ($form->isSubmitted() && $form->isValid())
        {
            $report = $form['report']->getData();

            $chartType = 'Line';

            $title = '';

            $em = $this->getDoctrine()->getManager();

            $data = [];

            $content = '';

            $myChart = $this->get('app.helper.chart_builder');

            if($report=='1')
            {
                $data = $em->getRepository('AppBundle:Court\Court')
                    ->findCourtTotalPerCategory();

                $chartType='Pie';

                $title = ' REPORT ON COURTS PER CATEGORY';

                $gridTemplate = '';

                $mainTemplate = 'main/app.report.list.html.twig';

                $regionTotals = null;

                $districtTotals = null;
            }
            else if($report=='2')
            {

                $title = ' REPORT ON COURTS PER REGION PER DISTRICT AND WARD';

                $data = $em->getRepository('AppBundle:Court\Court')
                    ->findCourtTotalPerRegionPerWard(false);

                $regionTotals = $em->getRepository('AppBundle:Court\Court')
                    ->findCourtTotalPerRegion(false);

                $districtTotals = $em->getRepository('AppBundle:Court\Court')
                    ->findCourtTotalDistricts(false);
              
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

                $data = $em->getRepository('AppBundle:Court\Court')
                    ->findCourtTotalPerRegionPerWard(true);

                $regionTotals = $em->getRepository('AppBundle:Court\Court')
                    ->findCourtTotalPerRegion(true);

                $districtTotals = $em->getRepository('AppBundle:Court\Court')
                    ->findCourtTotalDistricts(true);

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


            if($chartType=='Pie')
            {

                $xData = [];

                foreach ($data as $result)
                {
                    $myChart->setDataSeries($result['total'], $result['description'], null);

                    array_push($xData,$result['description']);
                }

                $value=implode(',',$xData);

                $myChart->setXData($value);

                $myChart->setChartTitle($title);

                $myChart->renderPieEchart();

                $content = $myChart->renderChartView();
            }
            else if ($chartType=='Line')
            {

                $xData = [];

                $yData = [];

                foreach ($results as $result)
                {

                    array_push($xData,"'$result[0]'");
                    array_push($yData,$result[1]);
                }

                $xValue = implode(',',$xData);
                $yValue = implode(',',$yData);

                $myChart->setDataSeries($yValue, 'Outdoor Count', 'line');

                $myChart->setXData($xValue);

                $myChart->setChartTitle('Outdoor Distribution');

                $myChart->setXAxisLabel('Company');

                $myChart->setYAxisLabel('Count');

                $myChart->renderBasicEchart();


                $myChart->setChartTitle($title);

                $myChart->renderBasicEchart();

                $content = $myChart->renderChartView();
            }


            $summary = array(
                $title
            );

            $data = array(
                'chart'=>$content,
                'summary'=>$summary,
                'title'=>$title
            );

            return $this->render('main/app.chart.html.twig',$data);

        }

        return $this->render(
            'main/app.form.html.twig',
            array(
                'formTemplate'=>'reports/visual.report',
                'form'=>$form->createView(),
                'title'=>'Visual Report Builder'
            )

        );



    }



}