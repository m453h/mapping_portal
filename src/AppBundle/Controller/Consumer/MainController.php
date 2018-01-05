<?php

namespace AppBundle\Controller\Consumer;

use AppBundle\Form\Consumer\DataLevelReportFormType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;


class MainController extends Controller
{

    /**
     * @Route("/", name="public_home_page")
     */
    public function homepageAction()
    {

        $datasets[0]=['name'=>'Average Number of Cases','icon'=>'bar-chart','path'=>'average-number-of-cases'];
        $datasets[1]=['name'=>'Number of Staff','icon'=>'users','path'=>'number-of-staff'];
        $datasets[2]=['name'=>'Building Conditions','icon'=>'building','path'=>'building-conditions'];
        $datasets[3]=['name'=>'Land Ownership','icon'=>'map','path'=>'land-ownership'];
        $datasets[4]=['name'=>'Environmental Conditions','icon'=>'tree','path'=>'environmental-conditions'];
        $datasets[5]=['name'=>'Social Activities','icon'=>'map-pointer','path'=>'social-activities'];


        $statistics[0] = ['label'=>'Court Levels','value'=>'5'];
        $statistics[1] = ['label'=>'Courts','value'=>'1340'];
        $statistics[2] = ['label'=>'Wards','value'=>'1400'];
        $statistics[3] = ['label'=>'Districts','value'=>'200'];
        $statistics[4] = ['label'=>'Zones','value'=>'10'];
        $statistics[5] = ['label'=>'Regions','value'=>'20'];

        $data['datasets'] = $datasets;
        $data['statistics'] = $statistics;


        return $this->render(
            'public/homepage.html.twig',
            $data
        );
    }


    /**
     * @Route("/dataset/{datasetName}", name="public_dataset_page")
     * @param $datasetName
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function socialActivitiesDatasetAction($datasetName)
    {

        switch($datasetName)
        {
            case 'social-activities':$data=$this->getSocialActivitiesDataset();break;
            case 'environmental-conditions':$data=$this->getEnvironmentalConditionsDataset();break;
            case 'average-number-of-cases':$data=$this->getAverageNumberOfCasesDataset();break;
            default: throw new NotFoundHttpException("Page Not Found");
        }


        return $this->render(
            'public/dataset.page.html.twig',
            $data
        );
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

}