<?php

namespace AppBundle\Controller\Administration\DataCollector;


use AppBundle\Entity\DataCollector\User;
use AppBundle\Entity\DataCollector\UserRegion;
use AppBundle\Entity\Location\Region;
use AppBundle\Form\DataCollector\AppUserRegionFormType;
use AppBundle\Form\DataCollector\DataCollectorRegionFormType;
use Pagerfanta\Adapter\DoctrineDbalAdapter;
use Pagerfanta\Pagerfanta;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DataCollectorRegionController extends Controller
{

    /**
     * @Route("/administration/data-collector-assign-regions/{userId}", name="data_collector_region_add",defaults={"userId":0})
     * @param Request $request
     * @param $userId
     * @return Response
     */
    public function newAction(Request $request,$userId)
    {

        $class = get_class($this);

        $this->denyAccessUnlessGranted('add',$class);

        $form = $this->createForm(DataCollectorRegionFormType::class);

        // only handles data on POST
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $userRegion = $form->getData();

            $em = $this->getDoctrine()->getManager();

            $user = $em->getRepository('AppBundle:DataCollector\User')
                ->find($userId);

            if(!($user instanceof User)) {

                $this->addFlash('error', 'You are trying to add a region to a user that does not exist');

                return $this->redirectToRoute('data_collectors_list');
            }

            $regions = $userRegion->getRegion();

            //Delete all programs under this given department before adding new departments
            $em->getRepository('AppBundle:DataCollector\UserRegion')
                ->deleteUserRegions($userId);

            foreach ($regions as $regionId)
            {
                $region = $em->getRepository('AppBundle:Location\Region')
                    ->find($regionId);

                $userRegion = new UserRegion();
                $userRegion->setUser($user);
                $userRegion->setRegion($region);
                $em->persist($userRegion);
                $em->flush();
            }

            $this->addFlash('success', 'Data Collector Region(s) successfully updated');

            return $this->redirectToRoute('data_collector_region_add',['userId'=>$userId]);
        }

        return $this->render(
            'administration/main/app.form.html.twig',
            array(
                'formTemplate'=>'data.collector/user.region',
                'form'=>$form->createView(),
                'title'=>'Data Collector Regions Details'
            )

        );
    }

}