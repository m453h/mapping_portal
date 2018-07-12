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

        $em = $this->getDoctrine()->getManager();

        $data['datasets'] = $em->getRepository('AppBundle:Consumer\DataSet')
            ->findAll();

        return $this->render(
            'public/homepage.html.twig',
            $data
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

        $qb1 = $em->getRepository('AppBundle:Court\Court')
            ->findAllCourts($options);

        $qb2 = $em->getRepository('AppBundle:Court\Court')
            ->countAllCourts($qb1);


        $adapter =new DoctrineDbalAdapter($qb1,$qb2);
        $dataGrid = new Pagerfanta($adapter);
        $dataGrid->setMaxPerPage(10);
        $dataGrid->setCurrentPage($page);
        $results = $dataGrid->getCurrentPageResults();

        $data = ['results'=>$results,'total'=>$dataGrid->count(),'records'=>$dataGrid];

        return $this->render(
            'public/search.results.page.html.twig',
            $data
        );
    }

    /**
     * @Route("/court-details", name="public_court_details")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function courtDetailsAction(Request $request)
    {

        $em = $this->getDoctrine()->getManager();

        $courtId = $request->get('courtId');

        $data = $em->getRepository('AppBundle:Court\Court')
            ->find($courtId);


        $data->getIsLandSurveyed() === true ? $data->setIsLandSurveyed("YES"): $data->setIsLandSurveyed("NO") ;

        $data->getHasTitleDeed() === true ?  $data->setHasTitleDeed("YES"): $data->setHasTitleDeed("NO") ;

        $data->getHasExtensionPossibility() === true ? $data->setHasExtensionPossibility("YES") : $data->setHasExtensionPossibility("NO");

        $data->getMeetsFunctionality() === true ? $data->setMeetsFunctionality("YES"): $data->setMeetsFunctionality("NO");

        $data->getHasLastMileConnectivity() === true ? $data->setHasLastMileConnectivity("YES"): $data->setHasLastMileConnectivity("NO");

        $data->getInternetAvailability() === true ? $data->setInternetAvailability("YES") : $data->setInternetAvailability("NO") ;

        if(!$data)
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
     * @Route("/dataset/{datasetName}", name="public_dataset_page")
     * @param Request $request
     * @param $datasetName
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function datasetPageAction(Request $request,$datasetName)
    {
        $em = $this->getDoctrine()->getManager();

        switch($datasetName)
        {
            case 'courts':$data=$this->getCourtDataset($request);break;
            case 'social-activities':$data=$this->getSocialActivitiesDataset();break;
            case 'environmental-conditions':$data=$this->getEnvironmentalConditionsDataset();break;
            case 'average-number-of-cases':$data=$this->getAverageNumberOfCasesDataset();break;
            default: throw new NotFoundHttpException("Page Not Found");
        }

        $dataset = $em->getRepository('AppBundle:Consumer\DataSet')
            ->findOneBy(['path'=>$datasetName]);

        $data['dataset'] = $dataset;

        return $this->render(
            'public/dataset.page.html.twig',
            $data
        );
    }

    public function getCourtDataset($request){

        $form = $this->createForm(DataLevelReportFormType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $em = $this->getDoctrine()->getManager();

            $courtLevel = $form['courtLevel']->getData();
            $region = $form['region']->getData();
            $ward = $form['ward']->getData();
            $district = $form['district']->getData();

            $options['sortBy'] = $request->query->get('sortBy');
            $options['sortType'] = $request->query->get('sortType');

            if($region!=null)
                $options['regionId'] = $region;

            if($district!=null)
                $options['districtId'] = $district;

            if($ward!=null)
                $options['wardId'] = $ward;

            if($courtLevel instanceof CourtLevel)
                $options['courtLevelId'] = $courtLevel->getLevelId();


            $courtData = $em->getRepository('AppBundle:Court\Court')
                ->findAllCourts($options)
                ->execute()
                ->fetchAll();

            $data['listData'] = $courtData;
        }


        $data['form'] = $form->createView();

        return $data;
    }

    public function getSocialActivitiesDataset(){
        $data['pageTitle'] = 'Social Activities';

        $data['description'] = 'Lorem ipsum dolor sit amet, stet iudicabit ius ei, no case reque has. Mea habeo ignota meliore cu. Sint wisi hendrerit 
        eu eum, populo invenire ad mea, vis cu sint atqui accumsan.
         Nec ut solum iriure. Dolores expetenda liberavisse id vel.
          Est indoctum moderatius liberavisse ei. 
          Nec ei feugiat conclusionemque, wisi sanctus ea his.';

        return $data;
    }

    public function getEnvironmentalConditionsDataset(){
        $data['pageTitle'] = 'Environmental Conditions';

        $data['description'] = 'Lorem ipsum dolor sit amet, stet iudicabit ius ei, no case reque has. Mea habeo ignota meliore cu. Sint wisi hendrerit 
        eu eum, populo invenire ad mea, vis cu sint atqui accumsan.
         Nec ut solum iriure. Dolores expetenda liberavisse id vel.
          Est indoctum moderatius liberavisse ei. 
          Nec ei feugiat conclusionemque, wisi sanctus ea his.';

        return $data;
    }

    public function getAverageNumberOfCasesDataset(){
        $data['pageTitle'] = 'Average Number of Cases';

        $data['description'] = 'Lorem ipsum dolor sit amet, stet iudicabit ius ei, no case reque has. Mea habeo ignota meliore cu. Sint wisi hendrerit 
        eu eum, populo invenire ad mea, vis cu sint atqui accumsan.
         Nec ut solum iriure. Dolores expetenda liberavisse id vel.
          Est indoctum moderatius liberavisse ei. 
          Nec ei feugiat conclusionemque, wisi sanctus ea his.';

        $form = $this->createForm(DataLevelReportFormType::class);

        $data['form']=$form->createView();

        return $data;
    }



    /**
     * @Route("/render-svg", name="public_court_details")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function svgRenderAction(Request $request)
    {

      $em = $this->getDoctrine()->getManager();


        return $this->render(
            'public/render.page.html.twig'

        );
    }



}