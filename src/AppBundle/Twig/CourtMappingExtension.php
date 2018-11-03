<?php

namespace AppBundle\Twig;


use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Twig_Extension;
use Twig_SimpleFilter;

class CourtMappingExtension extends Twig_Extension
{

    protected $container;
    /**
     * @var EntityManager
     */
    private $entityManager;


    public function __construct(ContainerInterface $container,EntityManager $entityManager)
    {
        $this->container = $container;
        $this->entityManager = $entityManager;
    }

    public function getName()
    {
        return 'course_results_resolver_Extension';
    }

    public function getFunctions() {
        return array(
            new \Twig_SimpleFunction('get_court_list', array($this, 'getCourtList'))
        );
    }

    public function getCourtList(){

        $results = $this->entityManager->getRepository('AppBundle:Configuration\CourtLevel')
            ->findAllCourtLevels(['sortType'=>null,'sortBy'=>null])
            ->execute()
            ->fetchAll();

        return $results;
    }
}