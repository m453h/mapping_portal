<?php

namespace AppBundle\Controller\Administration\Reports;

use AppBundle\Form\Reports\MapReportFormType;
use AppBundle\Form\Reports\PredefinedReportFormType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


class MapReportController extends Controller
{


    /**
     * @Route("/administration/map-report-builder", name="map_report_builder")
     * @param Request $request
     * @return Response
     */
    public function reportAction(Request $request)
    {

        $form = $this->createForm(MapReportFormType::class);

        // only handles data on POST
        $form->handleRequest($request);
       
        if ($form->isSubmitted() && $form->isValid())
        {
            $title = 'SPATIAL REPORT';

            $level = $form['courtLevel']->getData();
            $region = $form['region']->getData();
            $district = $form['district']->getData();
            $ward = $form['ward']->getData();

            $em = $this->getDoctrine()->getManager();

            $data = $em->getRepository('AppBundle:Court\Court')
                ->getAllCourts(['region'=>$region,'district'=>$district,'ward'=>$ward,'level'=>$level]);

            $points = [];
            $count = 0;
            foreach ($data as $item)
            {
                if(empty($item['court_name']))
                    $name = $item['ward_name'];
                else
                    $name = $item['court_name'];

                $points[$count]=sprintf('[%s, %s , "%s", "%s","%s","%s","%s",%s]',
                    $item['court_latitude'],
                    $item['court_longitude'],
                    $item['court_level'],
                    $name,
                    $item['region_name'],
                    $item['district_name'],
                    $item['ward_name'],
                    $item['court_id']
                    );

                $count++;

            }

            $points = implode(',',$points);


            $data = array(
                'points'=>"$points",
                'title'=>$title
            );

            return $this->render('main/app.map.html.twig',$data);

        }

        return $this->render(
            'main/app.form.html.twig',
            array(
                'formTemplate'=>'reports/map.report',
                'form'=>$form->createView(),
                'title'=>'Pre defined Report Builder'
            )

        );



    }



}