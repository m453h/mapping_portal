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

        $user = $this->get('security.token_storage')->getToken()->getUser();

        $template = 'dashboard/admin.dashboard.html.twig';

        $em = $this->getDoctrine()->getManager();

        $courtTotals = $em->getRepository('AppBundle:Court\Court')->findCourtTotalsByWard();

        $data = array(
            'moduleName'=>'Dashboard',
            /*'studentsTotal'=>$totalStudents,
            'staffTotal'=>$totalStaff,
            'departmentsTotal'=>$totalDepartments,
            'coursesTotal'=>$totalCourses,*/
            'courseCodes'=>implode(',',$courtTotals['wardNames']),
            'courseTotals'=>implode(',',$courtTotals['wardTotals'])
        );

        return $this->render(
            $template,
            $data
        );
    }

   







}