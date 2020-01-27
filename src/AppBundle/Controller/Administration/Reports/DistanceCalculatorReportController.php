<?php

namespace AppBundle\Controller\Administration\Reports;

use AppBundle\Form\Reports\DistanceCalculatorReportFormType;
use AppBundle\Form\Reports\MapReportFormType;
use AppBundle\Form\Reports\PredefinedReportFormType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


class DistanceCalculatorReportController extends Controller
{


    /**
     * @Route("/administration/distance-calculator-report-builder", name="distance_calculator_builder")
     * @param Request $request
     * @return Response
     */
    public function reportAction(Request $request)
    {

        $form = $this->createForm(DistanceCalculatorReportFormType::class);

        // only handles data on POST
        $form->handleRequest($request);

        $data['fromCourt'] = null;
        $data['toCourt'] = null;

        if ($form->isSubmitted() && $form->isValid())
        {
           $data = $form->getData();
        }

        $data['type'] = 'SPATIAL_DISTANCE';
        $data['routingURL'] = $this->getParameter('routing_engine');

        return $this->render(
            'administration/main/app.spatial.html.twig',
            array(
                'template'=>'administration/forms/reports/distance.calculator.report.form.html.twig',
                'form'=>$form->createView(),
                'isFullWidth'=>true,
                'data'=>$data,
                'title'=>strtoupper('Distance Calculator Report Builder')
            )

        );



    }



}