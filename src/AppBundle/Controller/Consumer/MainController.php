<?php

namespace AppBundle\Controller\Consumer;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;


class MainController extends Controller
{

    /**
     * @Route("/", name="public_home_page")
     */
    public function homepageAction()
    {

        $data = [];

        return $this->render(
            'public/homepage.html.twig',
            $data
        );
    }

   







}