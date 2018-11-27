<?php

namespace AppBundle\Controller\Administration\Reports;

use AppBundle\Form\Reports\CustomReportBuilderFormType;
use AppBundle\Form\Reports\MapReportFormType;
use AppBundle\Form\Reports\PredefinedReportFormType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


class ReportBuilderController extends Controller
{


    /**
     * @Route("/administration/custom-report-builder", name="report_builder_menu")
     * @param Request $request
     * @return Response
     */
    public function reportAction(Request $request)
    {

        $form = $this->createForm(CustomReportBuilderFormType::class);

        // only handles data on POST
        $form->handleRequest($request);
       
        if ($form->isSubmitted() && $form->isValid())
        {

            $fileName = 'JUDICIARY_REPORT';

            $em = $this->getDoctrine()->getManager();

            $level = $form['courtLevel']->getData();
            $region = $form['region']->getData();
            $district = $form['district']->getData();
            $ward = $form['ward']->getData();
            $columns = $form['columns']->getData();

            $columnMappings = [
                    'court_name'=>'court_name',
                    'court_code'=>'court_code',
                    'court_level'=>'l.description',
                    'land_ownership_status'=>'lo.description AS land_ownership',
                    'court_building_status'=>'bs.description AS building_status',
                    'environmental_status'=>'es.description AS environmental_status',
                    'year_constructed'=>'year_constructed',
                    'is_land_surveyed'=>'CASE WHEN is_land_surveyed THEN \'YES\' ELSE \'NO\' END',
                    'has_title_deed'=>'CASE WHEN has_title_deed THEN \'YES\' ELSE \'NO\' END',
                    'plot_number'=>'plot_number',
                    'meets_functionality'=>'CASE WHEN meets_functionality THEN \'YES\' ELSE \'NO\' END',
                    'has_last_mile_connectivity'=>'CASE WHEN has_last_mile_connectivity THEN \'YES\' ELSE \'NO\' END',
                    'number_of_computers'=>'number_of_computers',
                    'internet_availability'=>'CASE WHEN internet_availability THEN \'YES\' ELSE \'NO\' END',
                    'bandwidth'=>'bandwidth',
                    'available_systems'=>'available_systems',
                    'cases_per_year'=>'cases_per_year',
                    'population_served'=>'population_served',
                    'number_of_justices'=>'number_of_justices',
                    'number_of_judges'=>'number_of_judges',
                    'number_of_resident_magistrates'=>'number_of_resident_magistrates',
                    'number_of_district_magistrates'=>'number_of_district_magistrates',
                    'number_of_magistrates'=>'number_of_magistrates',
                    'number_of_court_clerks'=>'number_of_court_clerks',
                    'number_of_non_judiciary_staff'=>'number_of_non_judiciary_staff',
                    'court_latitude'=>'court_latitude',
                    'court_longitude'=>'court_longitude',
                    'areas_entitled'=>'areas_entitled',

                ];

            $data = $em->getRepository('AppBundle:Court\Court')
                ->getCourtsForCustomReportBuilder(['region'=>$region,'district'=>$district,'ward'=>$ward,'level'=>$level]);

            $title = 'CUSTOM REPORT';

            $grid = $this->get('app.helper.grid_builder');

            $grid->addGridHeader('S/N',null,'index');

            foreach ($columns as $column)
            {
                $grid->addGridHeader(str_replace('_',' ',$column),null,null,false);

                if(isset($columnMappings[$column]))
                {
                    $data->addSelect($columnMappings[$column]);
                }
                else{dump($column);}

            }

            $data = $data
                ->execute()
                ->fetchAll();

            $gridTemplate = 'administration/lists/base.list.html.twig';

            $mainTemplate = 'administration/main/app.report.list.html.twig';

            $regionTotals = null;

            $districtTotals = null;

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
            'administration/main/app.form.html.twig',
            array(
                'formTemplate'=>'reports/custom.report',
                'form'=>$form->createView(),
                'title'=>'Custom Report Builder'
            )

        );



    }



}