<?php

namespace AppBundle\Controller\Administration\Configuration;

use AppBundle\Entity\Configuration\Contact;
use AppBundle\Form\Configuration\ContactFormType;
use Pagerfanta\Adapter\DoctrineDbalAdapter;
use Pagerfanta\Pagerfanta;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


class ContactController extends Controller
{

    /**
     * @Route("/administration/contacts", name="contact_list")
     * @param Request $request
     * @return Response
     *
     */
    public function listAction(Request $request)
    {
        $class = get_class($this);
        
        $this->denyAccessUnlessGranted('view',$class);

        $page = $request->query->get('page',1);
        $options['sortBy'] = $request->query->get('sortBy');
        $options['sortType'] = $request->query->get('sortType');
        $options['name'] = $request->query->get('name');

        $maxPerPage = $this->getParameter('grid_per_page_limit');

        $em = $this->getDoctrine()->getManager();

        $qb1 = $em->getRepository('AppBundle:Configuration\Contact')
            ->findAllContacts($options);

        $qb2 = $em->getRepository('AppBundle:Configuration\Contact')
            ->countAllContacts($qb1);

        $adapter =new DoctrineDbalAdapter($qb1,$qb2);
        $dataGrid = new Pagerfanta($adapter);
        $dataGrid->setMaxPerPage($maxPerPage);
        $dataGrid->setCurrentPage($page);
        $dataGrid->getCurrentPageResults();
        
        //Configure the grid
        $grid = $this->get('app.helper.grid_builder');
        $grid->addGridHeader('S/N',null,'index');
        $grid->addGridHeader('Name','name','text',true);
        $grid->addGridHeader('Phone',null,'text',false);
        $grid->addGridHeader('E-mail',null,'text',false);
        $grid->addGridHeader('Actions',null,'action');
        $grid->setStartIndex($page,$maxPerPage);
        $grid->setPath('contact_list');
        $grid->setCurrentObject($class);
        $grid->setButtons();
        
        //Render the output
        return $this->render(
            'administration/main/app.list.html.twig',array(
                'records'=>$dataGrid,
                'grid'=>$grid,
                'title'=>'Existing Web Contacts',
                'gridTemplate'=>'administration/lists/base.list.html.twig'
        ));
    }

    /**
     * @Route("/administration/contacts/add", name="contact_add")
     * @param Request $request
     * @return Response
     */
    public function newAction(Request $request)
    {
        $class = get_class($this);
        
        $this->denyAccessUnlessGranted('add',$class);

        $form = $this->createForm(ContactFormType::class);

        // only handles data on POST
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $data = $form->getData();
            $em = $this->getDoctrine()->getManager();
            $em->persist($data);
            $em->flush();

            $this->addFlash('success','Contact successfully created');

            $this->get('app.helper.audit_trail_logger')
                ->logUserAction('CONFIGURATION\CONTACT','ADD',null,$data);

            return $this->redirectToRoute('contact_list');
        }

        return $this->render(
            'administration/main/app.form.html.twig',
            array(
                'formTemplate'=>'configuration/contact',
                'form'=>$form->createView(),
                'title'=>'Contact Details',
            )

        );
    }


    /**
     * @Route("/administration/contacts/edit/{contactId}", name="contact_edit")
     * @param Request $request
     * @param Contact $contact
     * @return Response
     */
    public function editAction(Request $request,Contact $contact)
    {
        $class = get_class($this);

        $this->denyAccessUnlessGranted('edit',$class);

        $form = $this->createForm(ContactFormType::class,$contact);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $data = $form->getData();

            $em = $this->getDoctrine()->getManager();
            $em->persist($data);
            $em->flush();

            $this->addFlash('success', 'Contact successfully updated!');

            $this->get('app.helper.audit_trail_logger')
                ->logUserAction('CONFIGURATION\CONTACT','EDIT',$contact,$data);

            return $this->redirectToRoute('contact_list');
        }

        return $this->render(
            'administration/main/app.form.html.twig',
            array(
                'formTemplate'=>'configuration/contact',
                'form'=>$form->createView(),
                'title'=>'Contact Details',
            )

        );
    }

    /**
     * @Route("/administration/contacts/delete/{Id}", name="contact_delete")
     * @param $Id
     * @return Response
     * @internal param Request $request
     */
    public function deleteAction($Id)
    {
        $class = get_class($this);
        
        $this->denyAccessUnlessGranted('delete',$class);

        $em = $this->getDoctrine()->getManager();

        $data = $em->getRepository('AppBundle:Configuration\Contact')->find($Id);

        if($data instanceof Contact)
        {
            $em->remove($data);
            $em->flush();
            $this->addFlash('success', 'Contact successfully removed !');

            $this->get('app.helper.audit_trail_logger')
                ->logUserAction('CONFIGURATION\CONTACTS','DELETE',$data,null);
        }
        else
        {
            $this->addFlash('error', 'Contact not found !');
        }

        
        return $this->redirectToRoute('contact_list');

    }
    
}