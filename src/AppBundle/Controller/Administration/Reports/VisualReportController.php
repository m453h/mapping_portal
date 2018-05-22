<?php

namespace AppBundle\Controller\Administration\Reports;

use AppBundle\Form\Reports\PredefinedReportFormType;
use AppBundle\Form\Reports\VisualReportFormType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


class VisualReportController extends Controller
{


    /**
     * @Route("/administration/visual-report-builder", name="visual_report_builder")
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

            $yAxisLabel = '';
            $xAxisLabel = '';

            $myChart = $this->get('app.helper.chart_builder');

            if($report=='1')
            {
                $data = $em->getRepository('AppBundle:Court\Court')
                    ->findCourtTotalPerCategory(true);

                $chartType='Pie';

                $title = 'COURTS DISTRIBUTION PER CATEGORY';

            }
            else if($report=='2')
            {
                $data = $em->getRepository('AppBundle:Court\Court')
                    ->findRegionCourtCasesReport();

                $chartType='Line';

                $title = 'REGIONS VS NO. CASES (MOST CASES)';

                $xAxisLabel = 'Regions';
                $yAxisLabel = 'Average No. Cases';

            }
            else if($report=='3')
            {
                $data = $em->getRepository('AppBundle:Court\Court')
                    ->findRegionLeastCasesReport();

                $chartType='Line';

                $title = 'REGIONS VS NO. CASES (LEAST CASES)';

                $xAxisLabel = 'Regions';
                $yAxisLabel = 'Average No. Cases';

            }
            else if($report=='4')
            {
                $data = $em->getRepository('AppBundle:Court\Court')
                    ->findEconomicActivityMostCasesReport();

                $chartType='Line';

                $title = 'ECONOMIC ACTIVITIES VS NO. CASES';

                $xAxisLabel = 'Activity';
                $yAxisLabel = 'Average No. Cases';
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

                foreach ($data as $result)
                {

                    array_push($xData,"'$result[description]'");
                    array_push($yData,$result['total']);
                }

                $xValue = implode(',',$xData);
                $yValue = implode(',',$yData);

                $myChart->setDataSeries($yValue, $yAxisLabel, 'line');

                $myChart->setXData($xValue);

                $myChart->setXAxisLabel($xAxisLabel);

                $myChart->setYAxisLabel($yAxisLabel);

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

            return $this->render('administration/main/app.chart.html.twig',$data);

        }

        return $this->render(
            'administration/main/app.form.html.twig',
            array(
                'formTemplate'=>'reports/visual.report',
                'form'=>$form->createView(),
                'title'=>'Visual Report Builder'
            )

        );



    }



}