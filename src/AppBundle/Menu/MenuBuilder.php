<?php


namespace AppBundle\Menu;

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
     * @param FactoryInterface $factory
     *
     * Add any other dependency you need
     * @param Router $router
     * @param TokenStorage $tokenStorage
     * @param RequestStack $requestStack
     */
    public function __construct(FactoryInterface $factory, Router $router,TokenStorage $tokenStorage,RequestStack $requestStack)
    {
        $this->factory = $factory;
        $this->router = $router;

        $this->tokenStorage = $tokenStorage;
        $this->requestStack = $requestStack;
    }

    public function createMainMenu()
    {
        $menu = $this->factory->createItem('root');

        $user = $this->tokenStorage->getToken()->getUser();

        $roleNames =$user->getRoles();

        //Set the Parent Menu Root Item
        $root = $menu->getRoot();
        $root->setChildrenAttributes(array('id' => 'sidebar-menu'));

        if(in_array('ROLE_ADMINISTRATOR',$roleNames))
        {
            $menu = $this->createAdminMenu($menu);
        }


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
            ->getParent();


        $menu->addChild('Locations', array('uri' => '#', 'extras' => array('icon' => 'map-marker')))
            ->addChild('Manage Zones', array('route' => 'zone_list', 'extras' => $this->getCrudLinks('zone')))->getParent()
            ->addChild('Manage Regions', array('route' => 'region_list', 'extras' => $this->getCrudLinks('region')))->getParent()
            ->addChild('Manage Districts', array('route' => 'district_list', 'extras' => $this->getCrudLinks('district')))->getParent()
            ->addChild('Manage Wards', array('route' => 'ward_list', 'extras' => $this->getCrudLinks('ward')))->getParent()
            ->addChild('Manage Village/Streets', array('route' => 'village_street_list', 'extras' => $this->getCrudLinks('village_street')))->getParent()
            ->getParent();



        $menu->addChild('App Users', array('uri' => '#', 'extras' => array('icon' => 'mobile')))
            ->addChild('Manage Users', array('route' => 'app_users_list', 'extras' => $this->getCrudLinks('app_user')))
            ->addChild('Manage Regions Assigned', array('route' => 'app_user_region_list', 'extras' => $this->getCrudLinks('app_user_region')))->setDisplay(false)
            ->getParent()
            ->getParent();


        $menu->addChild('Portal Users', array('uri' => '#', 'extras' => array('icon' => 'users')))
            //->addChild('Manage Regions', array('route' => 'region_list', 'extras' => $this->getCrudLinks('region')))->getParent()
            ->getParent();

        
        $menu->addChild('Documents', array('uri' => '#', 'extras' => array('icon' => 'book')))
            //->addChild('Manage Regions', array('route' => 'region_list', 'extras' => $this->getCrudLinks('region')))->getParent()
            ->getParent();

        $menu->addChild('Reports', array('uri' => '#', 'extras' => array('icon' => 'area-chart')))
            //->addChild('Manage Regions', array('route' => 'region_list', 'extras' => $this->getCrudLinks('region')))->getParent()
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
                        ['route' => $name.'_info'],
                        ['route' => $name.'_edit']
            ]
        ];

    }


}