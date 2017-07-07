<?php

namespace AppBundle\Controller;


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
     * @Route("/", name="security_login")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function loginAction(Request $request)
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
            'action' => $this->generateUrl('login_check')
        ]

        );

        return $this->render(
            'main/homepage.html.twig',
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
     * @Route("/login_check", name="login_check")
     */
    public function loginCheckAction()
    {
       return $this->redirectToRoute('security_login');
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
     * @Route("/logout", name="logout")
     */
    public function logoutAction()
    {
        throw new \Exception('This should not be reached');

    }


    /**
     * @Route("/api/sms", name="send_sms")
     * @param Request $request
     * @return JsonResponse
     */
    public function sendSMSAction(Request $request){

        $recipient = $request->get('recipient');
        $message  = $request->get('message');
        $apiKey  = $request->get('key');

        if($apiKey!='Lydia0001E2017')
        {
            $data['status']  = 'FAIL';
            $data['message'] = 'Invalid API Key';

            return new JsonResponse($data);
        }

        $counter = new SMSCounter();

        $counter->setTransactionDate(new \DateTime());

        $em = $this->getDoctrine()->getManager();

        $em->persist($counter);
        $em->flush();

        $id = $counter->getCounterId();

        if($id>=50)
        {
            $data['status'] = 'FAIL';
            $data['message'] = 'Limit exceeded please recharge';
        }
        else
        {

            $request = array(
                "authentication" => array(
                    "username" => "evolvetz",
                    "password" => "evolve67"
                ),

                "messages" => array(
                    array(
                        "sender" => "infoSMS",
                        "text" => $message,
                        "recipients" => array(
                            array(
                                "gsm" => $recipient
                            )
                        )
                    )
                )
            );

            $request = json_encode($request);
            $curl = curl_init();
            curl_setopt_array(
                $curl, array(
                    CURLOPT_CUSTOMREQUEST => "POST",
                    CURLOPT_RETURNTRANSFER => 1,
                    CURLOPT_URL => 'http://api.infobip.com/api/v3/sendsms/json',
                    CURLOPT_POSTFIELDS => $request,
                    CURLOPT_HTTPHEADER => array(
                        'Content-Type: application/json',
                        'Content-Length: ' . strlen($request)
                    )
                )
            );

            //Send the request & save response to $resp
            $response = curl_exec($curl);

            //Close request to clear up some resources
            curl_close($curl);

            $message = json_decode($response, true);

            $status = $message['results'][0]['status'];

            if ($status == '0') {
                $data['status'] = 'PASS';
            } else {
                $data['status'] = 'FAIL';
            }
        }

        return new JsonResponse($data);
    }


}