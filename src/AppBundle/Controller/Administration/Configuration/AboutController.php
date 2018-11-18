<?php

namespace AppBundle\Controller\Administration\Configuration;

use AppBundle\Entity\Configuration\Contact;
use AppBundle\Form\Configuration\AboutFormType;
use AppBundle\Form\Configuration\ContactFormType;
use Pagerfanta\Adapter\DoctrineDbalAdapter;
use Pagerfanta\Pagerfanta;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


class AboutController extends Controller
{


    /**
     * @Route("/administration/about-page", name="about_details")
     * @param Request $request
     * @return Response
     */
    public function newAction(Request $request)
    {
        $class = get_class($this);
        
        $this->denyAccessUnlessGranted('edit',$class);

        $about = $this->getDoctrine()->getRepository('AppBundle:Configuration\About')
            ->findOneBy(['aboutId'=>1]);

        $form = $this->createForm(AboutFormType::class,$about);

        // only handles data on POST
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $data = $form->getData();
            $em = $this->getDoctrine()->getManager();
            $em->persist($data);
            $em->flush();

            $this->addFlash('success','About page content successfully updated');

            $this->get('app.helper.audit_trail_logger')
                ->logUserAction('CONFIGURATION\ABOUT','UPDATE',null,$data);

            return $this->redirectToRoute('about_details');
        }

        return $this->render(
            'administration/main/app.form.html.twig',
            array(
                'formTemplate'=>'configuration/about',
                'form'=>$form->createView(),
                'title'=>'About Page Details',
            )

        );
    }

}