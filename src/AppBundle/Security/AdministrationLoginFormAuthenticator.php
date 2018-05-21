<?php


namespace AppBundle\Security;


use AppBundle\Entity\UserAccounts\User;
use AppBundle\Form\Accounts\LoginForm;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoder;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\Authenticator\AbstractFormLoginAuthenticator;

class AdministrationLoginFormAuthenticator extends AbstractFormLoginAuthenticator
{

    private $formFactory;
    private $em;
    private $router;
    private $passwordEncoder;

    /**
     * @var RequestStack
     */
    private $request;

    public function __construct(FormFactoryInterface $formFactory, EntityManager $em, RouterInterface $router,UserPasswordEncoder $passwordEncoder,RequestStack $request)
    {
        $this->formFactory = $formFactory;
        $this->em = $em;
        $this->router = $router;
        $this->passwordEncoder = $passwordEncoder;
        $this->request = $request;
    }


    public function getCredentials(Request $request)
    {
        $isLoginSubmit = (($request->getPathInfo() == '/administration_login_check')) && $request->isMethod('POST');

        if(!$isLoginSubmit){
            return null; //Return nothing if null brings issues
        }

        $form = $this->formFactory->create(LoginForm::class);

        $form->handleRequest($request);

        $data =$form->getData();

        if(empty($data['_username']) || empty($data['_password']))
            throw new CustomUserMessageAuthenticationException(
                'Please provide all the required information'
            );


        $request->getSession()->set(
            Security::LAST_USERNAME,
            $data['_username']
        );

        return $data;

    }


    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        $username = $credentials['_username'];

        $user = $this->em->getRepository('AppBundle:UserAccounts\User')
            ->findOneBy(['username' => $username]);

        return $user;
    }

    public function checkCredentials($credentials, UserInterface $user)
    {
       $password = $credentials['_password'];

        if ($this->passwordEncoder->isPasswordValid($user, $password)) {
            return true; 
        }

        return false;
    }
    
    protected function getLoginUrl()
    {
        return $this->router->generate('admin_security_login');
    }

    protected function getDefaultSuccessRedirectUrl()
    {

       return $this->router->generate('app_home_page');
    }

}