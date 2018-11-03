<?php

namespace AppBundle\Controller\Consumer;

use AppBundle\Entity\Configuration\CourtLevel;
use AppBundle\Form\Consumer\DataLevelReportFormType;
use Pagerfanta\Adapter\DoctrineDbalAdapter;
use Pagerfanta\Pagerfanta;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;


class MainController extends Controller
{

    /**
     * @Route("/", name="public_home_page")
     */
    public function homepageAction()
    {

        return $this->render(
            'public/homepage.html.twig',
            []
        );
    }


    /**
     * @Route("/search-results", name="public_search_results")
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
            $data = ['results'=>[],'total'=>0,'paginate'=>false];

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

        $data = ['results'=>$results,'total'=>$dataGrid->count(),'records'=>$dataGrid,'paginate'=>true];

        return $this->render(
            'public/search.results.page.html.twig',
            $data
        );
    }

    /**
     * @Route("/court-details/court-name", name="public_court_details")
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
        ];

        return $this->render(
            'public/court.details.page.html.twig',
            $data
        );
    }


    /**
     * @Route("/court-level-description/{level}", name="public_court_level_description")
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
                'courtLevel'=>$courtLevel
            ]
        );
    }

}