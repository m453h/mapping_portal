<?php

namespace AppBundle\Twig;


use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Twig_Extension;
use Twig_SimpleFilter;

class CourtMappingExtension extends Twig_Extension
{

    protected $container;
    /**
     * @var EntityManager
     */
    private $entityManager;
    /**
     * @var RequestStack
     */
    private $requestStack;


    public function __construct(ContainerInterface $container,EntityManager $entityManager,RequestStack $requestStack)
    {
        $this->container = $container;
        $this->entityManager = $entityManager;
        $this->requestStack = $requestStack;
    }

    public function getName()
    {
        return 'course_results_resolver_Extension';
    }

    public function getFunctions() {
        return array(
            new \Twig_SimpleFunction('get_court_list', array($this, 'getCourtList')),
            new \Twig_SimpleFunction('highlight_public_menu', array($this, 'highlightPublicMenu')),
            new \Twig_SimpleFunction('get_current_access_level', array($this, 'getCurrentAccessLevel'))
        );
    }

    /**
     * @param $locale
     * @return array
     */
    public function getCourtList($locale){

        $results = $this->entityManager->getRepository('AppBundle:Configuration\CourtLevel')
            ->findAllCourtLevels(['sortType'=>null,'sortBy'=>null,'locale'=>$locale])
            ->execute()
            ->fetchAll();

        return $results;
    }

    public function highlightPublicMenu($currentPosition,$expectedValue){
        if($currentPosition==$expectedValue)
            return 'selected';

        return null;
    }

    public function getCurrentAccessLevel(){
      $requestURI = ($this->requestStack->getCurrentRequest()->getRequestUri());

      $requestURI = explode('/',$requestURI);

      if(in_array('administration',$requestURI))
      {
          return 'app_home_page';
      }

      return 'public_home_page';
    }
}