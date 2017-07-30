<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;


class MainController extends Controller
{

    /**
     * @Route("/home", name="app_home_page")
     */
    public function homepageAction()
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $template = 'dashboard/admin.dashboard.html.twig';

        $em = $this->getDoctrine()->getManager();

        $courtTotals = $em->getRepository('AppBundle:Court\Court')->findCourtTotalsByRegion();

        $validDataCount = $em->getRepository('AppBundle:Court\Court')->findCourtTotalByStatus(true);

        $testDataCount = $em->getRepository('AppBundle:Court\Court')->findCourtTotalByStatus(false);

        $userCount = $em->getRepository('AppBundle:AppUsers\User')->findTotalAppUsers();

        $wardCount = $em->getRepository('AppBundle:Location\Ward')->findTotalWards();


        $data = array(
            'validDataCount'=>$validDataCount,
            'testDataCount'=>$testDataCount,
            'userCount'=>$userCount,
            'wardCount'=>$wardCount,
            'names'=>implode(',',$courtTotals['names']),
            'totals'=>implode(',',$courtTotals['totals'])
        );

        return $this->render(
            $template,
            $data
        );
    }

   







}