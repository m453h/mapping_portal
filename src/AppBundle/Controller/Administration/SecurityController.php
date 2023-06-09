<?php

namespace AppBundle\Controller\Administration;


use AppBundle\Entity\UserAccounts\SMSCounter;
use AppBundle\Form\Accounts\LoginForm;
use AppBundle\Form\Accounts\ResetPasswordForm;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;

class SecurityController extends  Controller
{

    /**
     * @Route("/administration", name="admin_security_login")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function loginAction()
    {

        $key = '_security.main.target_path'; # where "main" is your firewall name

        if ($this->get('session')->has($key))
        {
            $this->addFlash('error','You need to login to view this page');
        }

        $authenticationUtils = $this->get('security.authentication_utils');

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        $form = $this->createForm(LoginForm::class,[
            '_username'=>$lastUsername //if you fail to login pre-populate username
        ], [
            'action' => $this->generateUrl('admin_login_check')
        ]

        );

        return $this->render(
            'administration/main/homepage.html.twig',
            array(
                // last username entered by the user
                'form' => $form->createView(),
                'error' => $error,
                'section' =>'outer',
                'formTemplate' =>'user.accounts/login'
            )
        );

    }


    /**
     * @Route("/api/privacy-policy", name="app_privacy_policy")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function privacyPolicyAction()
    {
        
        return $this->render(
            'administration/main/privacy.policy.html.twig',
            array(
            )
        );

    }

    /**
     * @Route("/administration_login_check", name="admin_login_check")
     */
    public function loginCheckAction()
    {
        return $this->redirectToRoute('admin_security_login');
    }

    /**
     * @Route("/token-authentication/reset-password", name="security_password_reset")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function resetPasswordAction(Request $request)
    {
        $user=$this->getUser();
        $form = $this->createForm(ResetPasswordForm::class,$user, ['action' => $this->generateUrl('security_password_reset')]);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $confirmPassword = $form['plainPasswordConfirm']->getData();
            $canChange = true;



            /*if($canChange === false)
                return $this->redirectToRoute('security_password_reset');
            */

        }
        return $this->render(
            'main/homepage.html.twig',
            array(
                // last username entered by the user
                'form' => $form->createView(),
                'error'=>null,
                'section' =>'outer',
                'formTemplate'=>'user.accounts/reset.password'
            )
        );

    }


    /**
     * @Route("/token-authentication/{token}", name="token_authentication")
     */
    public function authenticationCheckAction()
    {

        return $this->redirectToRoute('security_password_reset');
    }




    /**
     * @Route("/administration/logout", name="admin_logout")
     */
    public function logoutAction()
    {
        throw new \Exception('This should not be reached');

    }

}