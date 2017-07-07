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

        $roleName =$user->getRoles();

        $template = 'main/app.dashboard.html.twig';

        $data = array ();

        if(in_array('ROLE_STUDENT',$roleName))
        {
            $template = 'dashboard/student.dashboard.html.twig';

            $em = $this->getDoctrine()->getManager();

            $admission = $em->getRepository('AppBundle:Registration\Student\StudentAdmission')
                ->getCurrentAdmission($user->getUsername());

            $admissionId = $admission->getAdmissionId();
            
            $curriculumId = $admission->getCurriculum()->getCurriculumId();

            $totalModules = $em->getRepository('AppBundle:Configuration\CurriculumModule')
                ->findCurriculumTotalModules(null,$curriculumId);

            $passedModules = $em->getRepository('AppBundle:Registration\Student\EnrolledModule')
                ->countModuleResultsByStatus($admissionId,'PASS');

            $failedModules = $em->getRepository('AppBundle:Registration\Student\EnrolledModule')
                ->countModuleResultsByStatus($admissionId,'FAILED');

            $remainingModules = $totalModules -  ($passedModules+$failedModules);
            $data = array(
                'moduleName'=>'Dashboard',
                'totalModules'=>$totalModules,
                'passedModules'=>$passedModules,
                'failedModules'=>$failedModules,
                'remainingModules'=>$remainingModules,
                'admission'=>$admission
            );
        }

        return $this->render(
            $template,
            $data
        );
    }

   







}