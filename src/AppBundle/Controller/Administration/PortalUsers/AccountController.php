<?php

namespace AppBundle\Controller\Administration\PortalUsers;

use AppBundle\Entity\Configuration\Course;
use AppBundle\Entity\UserAccounts\Staff;
use AppBundle\Entity\UserAccounts\User;
use AppBundle\Form\Accounts\ResetMyPasswordForm;
use AppBundle\Form\Accounts\ResetPasswordForm;
use AppBundle\Form\Accounts\StaffRoleFormType;
use AppBundle\Form\Accounts\UserFormType;
use Exception;
use Pagerfanta\Adapter\DoctrineDbalAdapter;
use Pagerfanta\Pagerfanta;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;


class AccountController extends Controller
{


    /**
     * @Route("/administration/change-my-password", name="change_account_password")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function changePasswordAction(Request $request)
    {
        $user = $this->getUser();

        $form = $this->createForm(ResetMyPasswordForm::class,$user);

        // only handles data on POST
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $em = $this->getDoctrine()->getManager();

            $data = $form->getData();

            $currentPassword = $form['currentPassword']->getData();

            $password = $form['plainPassword']->getData();

            $currentPasswordHash = $em->getRepository('AppBundle:UserAccounts\User')
                ->findCurrentUserHash($user->getId());

            $encoderService = $this->get('security.encoder_factory');
            $encoder = $encoderService->getEncoder($user);

            if ($encoder->isPasswordValid($currentPasswordHash, $currentPassword, $user->getSalt()))
            {
                if ($encoder->isPasswordValid($currentPasswordHash, $password, $user->getSalt()))
                {

                    $this->addFlash('error', 'You can not use your current password as the new password');

                    return $this->redirectToRoute('change_account_password');
                }

                $encoder = $this->get('security.password_encoder');
                $data->setPassword($encoder->encodePassword($user,$password));
                $data->setLastPasswordUpdateDate(new \DateTime());

                $em->persist($data);
                $em->flush();
            }
            else
            {
                $this->addFlash('error', 'Invalid current password entered, please type your password carefully again !');

                return $this->redirectToRoute('change_account_password');
            }

            $this->addFlash('success', 'Password successfully changed');
            return $this->redirectToRoute('change_account_password');
        }


        return $this->render(
            'administration/main/app.form.html.twig',
            array(
                'formTemplate'=>'my.account/reset.password',
                'form'=>$form->createView(),
                'title'=>'Password change form'
            )

        );
    }
    
}