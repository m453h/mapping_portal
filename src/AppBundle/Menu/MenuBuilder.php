<?php


namespace AppBundle\Menu;

use Doctrine\ORM\EntityManager;
use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Knp\Menu\Matcher;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

class MenuBuilder
{

    private $factory;
   
    /**
     * @var Router
     */
    private $router;
    /**
     * @var TokenStorage
     */
    private $tokenStorage;
    /**
     * @var RequestStack
     */
    private $requestStack;
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @param FactoryInterface $factory
     *
     * Add any other dependency you need
     * @param Router $router
     * @param TokenStorage $tokenStorage
     * @param RequestStack $requestStack
     * @param EntityManager $entityManager
     */
    public function __construct(FactoryInterface $factory, Router $router,TokenStorage $tokenStorage,RequestStack $requestStack,EntityManager $entityManager)
    {
        $this->factory = $factory;
        $this->router = $router;

        $this->tokenStorage = $tokenStorage;
        $this->requestStack = $requestStack;
        $this->entityManager = $entityManager;
    }

    public function createMainMenu()
    {
        $menu = $this->factory->createItem('root');

        $user = $this->tokenStorage->getToken()->getUser();

        $roleNames =$user->getRoles();

        //Set the Parent Menu Root Item
        $root = $menu->getRoot();
        $root->setChildrenAttributes(array('id' => 'sidebar-menu'));

        $menu = $this->createAdminMenu($menu);

        return $menu;
    }

    public function createAdminMenu(ItemInterface $menu)
    {

        $menu->addChild('Configuration', array('uri' => '#', 'extras' => array('icon' => 'cogs')))
            ->addChild('Court Level', array('route' => 'court_level_list', 'extras' => $this->getCrudLinks('court_level')))->getParent()
            ->addChild('Court Building Status', array('route' => 'court_building_status_list', 'extras' => $this->getCrudLinks('court_building_status')))->getParent()
            ->addChild('Court Building Ownership Status', array('route' => 'court_building_ownership_status_list', 'extras' => $this->getCrudLinks('court_building_ownership_status')))->getParent()
            ->addChild('Economic Activity', array('route' => 'economic_activity_list', 'extras' => $this->getCrudLinks('economic_activity')))->getParent()
            ->addChild('Land Ownership Status', array('route' => 'land_ownership_status_list', 'extras' => $this->getCrudLinks('land_ownership_status')))->getParent()
            ->addChild('Transport Mode', array('route' => 'transport_mode_list', 'extras' => $this->getCrudLinks('transport_mode')))->getParent()
            ->addChild('Court Environmental Status', array('route' => 'court_environmental_status_list', 'extras' => $this->getCrudLinks('court_environmental_status')))->getParent()
            ->addChild('Land use', array('route' => 'land_use_list', 'extras' => $this->getCrudLinks('land_use')))->getParent()
            ->addChild('Web Contacts', array('route' => 'contact_list', 'extras' => $this->getCrudLinks('contact')))->getParent()
            ->addChild('Web About', array('route' => 'about_details', 'extras' => $this->getCrudLinks('about_details')))->getParent()
            ->getParent();


        $menu->addChild('Locations', array('uri' => '#', 'extras' => array('icon' => 'map-marker')))
            ->addChild('Manage Zones', array('route' => 'zone_list', 'extras' => $this->getCrudLinks('zone')))->getParent()
            ->addChild('Manage Regions', array('route' => 'region_list', 'extras' => $this->getCrudLinks('region')))->getParent()
            ->addChild('Manage Districts', array('route' => 'district_list', 'extras' => $this->getCrudLinks('district')))->getParent()
            ->addChild('Manage Wards', array('route' => 'ward_list', 'extras' => $this->getCrudLinks('ward')))->getParent()
            ->addChild('Manage Village/Streets', array('route' => 'village_street_list', 'extras' => $this->getCrudLinks('village_street')))->getParent()
            ->getParent();

        $menu->addChild('Court Data', array('uri' => '#', 'extras' => array('icon' => 'book')))
            ->addChild('Manage Existing Courts', array('route' => 'court_form_list', 'extras' => $this->getCrudLinks('court_form')))
            ->addChild('View Court Details', array('route' => 'court_form_info'))->setDisplay(false)
            ->getParent()
            ->getParent()
            ->getParent();


        $menu->addChild('Reports', array('uri' => '#', 'extras' => array('icon' => 'area-chart')))
            ->addChild('Pre-defined reports', array('route' => 'pre_defined_report_builder'))->getParent()
            ->addChild('Visual reports', array('route' => 'visual_report_builder', 'extras' => $this->getCrudLinks('region')))->getParent()
            ->addChild('Map reports', array('route' => 'map_report_builder', 'extras' => $this->getCrudLinks('region')))->getParent()
            ->addChild('Distance calculator', array('route' => 'distance_calculator_builder', 'extras' => $this->getCrudLinks('region')))->getParent()
            ->addChild('Report builder', array('route' => 'report_builder_menu', 'extras' => $this->getCrudLinks('region')))->getParent()
            ->getParent();

        $menu->addChild('User Accounts', array('uri' => '#', 'extras' => array('icon' => 'users')))
            ->addChild('Manage Data Collectors', array('route' => 'data_collectors_list', 'extras' => $this->getCrudLinks('data_collectors')))
            ->addChild('View user details', array('route' => 'data_collectors_info'))->setDisplay(false)->getParent()
            ->addChild('Manage Regions Assigned', array('route' => 'data_collector_region_add'))->setDisplay(false)
            ->getParent()
            ->getParent()
            ->addChild('Manage Portal Users', array('route' => 'portal_users_list', 'extras' => $this->getCrudLinks('portal_users')))
            ->addChild('Portal users details', array('route' => 'portal_users_info'))->setDisplay(false)->getParent()
            ->addChild('Portal users role assignment', array('route' => 'user_assign_role'))->setDisplay(false)->getParent()->getParent()
            ->addChild('Manage Portal User Roles', array('route' => 'defined_roles_list'))->setDisplay(true)->getParent()
            ->addChild('Reset user password', array('route' => 'user_password_reset'))->setDisplay(false)
            ->getParent();



        $menu->addChild('My Account', array('uri' => '#', 'extras' => array('icon' => 'wrench')))
            ->addChild('Change Password', array('route' => 'change_account_password'))->getParent()
            ->getParent();


        return $menu;
    }



    public function getParameter($name)
    {
        return $this->requestStack->getCurrentRequest()->get($name);
    }

    public function getCrudLinks($name)
    {
       return [
            'routes' => [
                        ['route' => $name.'_add'],
                        ['route' => $name.'_edit']
            ]
        ];

    }


}