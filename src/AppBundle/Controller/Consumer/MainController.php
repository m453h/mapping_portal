<?php

namespace AppBundle\Controller\Consumer;

use Pagerfanta\Adapter\DoctrineDbalAdapter;
use Pagerfanta\Pagerfanta;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;


class MainController extends Controller
{

    /**
     * @Route("/{_locale}", name="public_home_page",defaults={"_locale":"en"},requirements={"_locale":"en|sw"})
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function homepageAction()
    {


        return $this->render(
            'public/homepage.html.twig',
            ['currentPosition'=>'home']
        );
    }


    /**
     * @Route("/{_locale}/search-results", name="public_search_results",defaults={"_locale":"en"},requirements={"_locale":"en|sw"})
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function searchResultsAction(Request $request)
    {

        $em = $this->getDoctrine()->getManager();

        $page = $request->query->get('page',1);
        $options['sortBy'] = $request->query->get('sortBy');
        $options['sortType'] = $request->query->get('sortType');
        $options['locationWithName'] = $request->query->get('q');
        $options['dataType'] = 'VERIFIED';

        if(empty($options['locationWithName']))
        {
            $data = ['results'=>[],'total'=>0,'paginate'=>false,'currentPosition'=>'search'];

            return $this->render(
                'public/search.results.page.html.twig',
                $data
            );
        }

        $qb1 = $em->getRepository('AppBundle:Court\Court')
            ->findAllCourts($options);

        $qb2 = $em->getRepository('AppBundle:Court\Court')
            ->countAllCourts($qb1);

        $adapter =new DoctrineDbalAdapter($qb1,$qb2);
        $dataGrid = new Pagerfanta($adapter);
        $dataGrid->setMaxPerPage(10);
        $dataGrid->setCurrentPage($page);
        $results = $dataGrid->getCurrentPageResults();

        $data = ['results'=>$results,'total'=>$dataGrid->count(),'records'=>$dataGrid,'paginate'=>true,'currentPosition'=>'search'];

        return $this->render(
            'public/search.results.page.html.twig',
            $data
        );
    }

    /**
     * @Route("/{_locale}/court-details/court-name", name="public_court_details",defaults={"_locale":"en"},requirements={"_locale":"en|sw"})
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function courtDetailsAction(Request $request)
    {

        $em = $this->getDoctrine()->getManager();

        $courtId = $request->get('courtId');

        $data = $em->getRepository('AppBundle:Court\Court')
            ->find($courtId);

        if(!$data)
        {
            throw new NotFoundHttpException('Court Not Found');
        }

        if($data->getCourtRecordStatus()==false || $data->getCourtVerificationStatus()==false || $data->getIsPlotOnly()==true)
        {
            throw new NotFoundHttpException('Court Not Found');
        }

        $coordinates['latitude'] = $data->getCourtLatitude();
        $coordinates['longitude'] = $data->getCourtLongitude();

        $images = [];

        if($data->getFirstCourtView()!=null)
        {
            array_push($images, $data->getFirstCourtView());
        }

        if($data->getSecondCourtView()!=null)
        {
            array_push($images, $data->getSecondCourtView());
        }

        if($data->getThirdCourtView()!=null)
        {
            array_push($images, $data->getThirdCourtView());
        }

        if($data->getFourthCourtView()!=null)
        {
            array_push($images, $data->getFourthCourtView());
        }

        $data = ['court'=>$data,
            'images'=>$images,
            'coordinates'=>$coordinates,
            'currentPosition'=>'search'
        ];

        return $this->render(
            'public/court.details.page.html.twig',
            $data
        );
    }


    /**
     * @Route("/{_locale}/court-level-description/{level}", name="public_court_level_description", defaults={"_locale":"en"},requirements={"_locale":"en|sw"})
     * @param $level
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function courtLevelDescriptionAction($level)
    {
        $em = $this->getDoctrine()->getManager();

        $courtLevel = $em->getRepository('AppBundle:Configuration\CourtLevel')
            ->findOneBy(['identifier'=>$level]);

        return $this->render(
            'public/court.level.details.page.html.twig', [
                'courtLevel'=>$courtLevel,
                'currentPosition'=>'courts'
            ]
        );
    }


    /**
     * @Route("/{_locale}/contact", name="public_contact", defaults={"_locale":"en"},requirements={"_locale":"en|sw"})
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function contactAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $qb1 = $em->getRepository('AppBundle:Configuration\Contact')
            ->findAllContacts(['sortBy'=>null,'sortType'=>'DESC'])
            ->addSelect('address,road,fax');

        $qb2 = $em->getRepository('AppBundle:Configuration\Contact')
            ->countAllContacts($qb1);

        $page = $request->query->get('page',1);

        $adapter =new DoctrineDbalAdapter($qb1,$qb2);
        $dataGrid = new Pagerfanta($adapter);
        $dataGrid->setMaxPerPage(20);
        $dataGrid->setCurrentPage($page);
        $dataGrid->getCurrentPageResults();

        return $this->render(
            'public/contact.page.html.twig', [
                'records'=>$dataGrid,
                'contacts'=>$dataGrid->getCurrentPageResults(),
                'currentPosition'=>'contact'
            ]
        );
    }


    /**
     * @Route("/{_locale}/about", name="public_about", defaults={"_locale":"en"},requirements={"_locale":"en|sw"})
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function aboutAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $about = $em->getRepository('AppBundle:Configuration\About')
            ->find(1);

        return $this->render(
            'public/about.page.html.twig', [
                'about'=>$about,
                'currentPosition'=>'about'
            ]
        );
    }














    /**
     * @Route("/locale-changer/{_locale}", name="public_locale_changer")
     * @param Request $request
     * @param $_locale
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function localeChangeAction(Request $request,$_locale)
    {

        return $this->redirectToRoute('public_home_page',['_locale'=>$_locale]);
    }


}