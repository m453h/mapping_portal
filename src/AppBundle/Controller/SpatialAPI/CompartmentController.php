<?php

namespace AppBundle\Controller\SpatialAPI;

use Ddeboer\DataImport\Reader\CsvReader;
use Pagerfanta\Adapter\DoctrineDbalAdapter;
use Pagerfanta\Pagerfanta;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;


class CompartmentController extends Controller
{

    /**
     * @Route("/spatialAPI/get-compartment-list", options={"expose"=true},name="compartment_spatial_data")
     * @return Response
     *
     */
    public function listAction()
    {
        $em = $this->getDoctrine()->getManager();

        $response = $em->getRepository('AppBundle:Location\Compartment')
            ->getCompartmentGeometry([]);

        $leafletTransformer = $this->get('app.helper.leaflet_data_transformer');

        $response = $leafletTransformer->formatArrayToString($response);

        return new Response($response);
    }
    
}