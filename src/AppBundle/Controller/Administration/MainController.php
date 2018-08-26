<?php

namespace AppBundle\Controller\Administration;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;


class MainController extends Controller
{

    /**
     * @Route("/administration/home", name="app_home_page")
     */
    public function homepageAction(Request $request)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $template = 'administration/dashboard/admin.dashboard.html.twig';

        $em = $this->getDoctrine()->getManager();

        $courtTotals = $em->getRepository('AppBundle:Court\Court')->findCourtTotalsByRegion();

        $validDataCount = $em->getRepository('AppBundle:Court\Court')->findCourtTotalByStatus(true);

        $testDataCount = $em->getRepository('AppBundle:Court\Court')->findCourtTotalByStatus(false);

        $verificationDataCount = $em->getRepository('AppBundle:Court\Court')->findCourtTotalByVerificationStatus(true);

        $userCount = $em->getRepository('AppBundle:DataCollector\User')->findTotalAppUsers();

        $wardCount = $em->getRepository('AppBundle:Location\Ward')->findTotalWards();

        $court = $em->getRepository('AppBundle:Court\Court')
            ->findOneBy(['courtId'=>987]);

        $data = array(
            'validDataCount'=>$validDataCount,
            'testDataCount'=>$testDataCount,
            'verificationDataCount'=>$verificationDataCount,
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