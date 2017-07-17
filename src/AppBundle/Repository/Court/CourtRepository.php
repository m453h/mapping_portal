<?php

namespace AppBundle\Repository\Court;

use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\ORM\EntityRepository;

class CourtRepository extends EntityRepository
{

    /**
     * @param array $options
     * @return QueryBuilder
     */
    public function findAllCourts($options = [])
    {

        $conn = $this->getEntityManager()->getConnection();

        $queryBuilder = new QueryBuilder($conn);
        $queryBuilder->select('status_id,description')
            ->from('cfg_court_building_ownership_status', 's');
        $queryBuilder = $this->setFilterOptions($options, $queryBuilder);
        $queryBuilder = $this->setSortOptions($options, $queryBuilder);

        return $queryBuilder;
    }

    public function setFilterOptions($options, QueryBuilder $queryBuilder)
    {
        if (!empty($options['name']))
        {
            return $queryBuilder->andWhere('lower(description) LIKE lower(:name)')
                ->setParameter('name', '%' . $options['name'] . '%');
        }

        return $queryBuilder;
    }

    public function setSortOptions($options, QueryBuilder $queryBuilder)
    {

        $options['sortType'] == 'desc' ? $sortType = 'desc' : $sortType = 'asc';

         if ($options['sortBy'] === 'name')
         {
             return $queryBuilder->addOrderBy('description', $sortType);
         }

        return $queryBuilder->addOrderBy('status_id', 'desc');

    }

    public function countAllCourts(QueryBuilder $queryBuilder)
    {
        return function ($queryBuilder) {
            $queryBuilder->select('COUNT(DISTINCT status_id) AS total_results')
                ->groupBy('status_id')
                ->resetQueryPart('orderBy')
                ->resetQueryPart('groupBy')
                ->setMaxResults(1);
        };
    }


    /**
     * @param $data
     */
    public function recordCourtDetails($data)
    {
        $conn = $this->getEntityManager()->getConnection();

        $queryBuilder = new QueryBuilder($conn);

        $queryBuilder
            ->insert('tbl_court_details')
            ->setValue('level_id',':courtLevelId')
            ->setValue('category_id',':courtCategoryId')
            ->setValue('ward_id',':wardId')
            ->setValue('land_ownership_status_id',':landOwnershipStatusId')
            ->setValue('is_land_surveyed',':landSurveyStatus')
            ->setValue('has_title_deed',':titleDeedStatus')
            ->setValue('title_deed',':titleDeedNo')
            ->setValue('building_ownership_status_id',':buildingOwnershipStatusId')
            ->setValue('court_building_status_id',':buildingStatusId')
            ->setValue('functionality',':functionality')
            ->setValue('it_Provision',':ITProvision')
            ->setValue('year_constructed',':yearConstructed')
            ->setValue('has_extension_possibility',':extensionPossibility')
            ->setValue('cases_per_year',':casesPerYear')
            ->setValue('population_served',':populationServed')
            ->setValue('number_of_Justices',':numberOfJustices')
            ->setValue('non_judiciary',':nonJudiciary')
            ->setValue('total_staff',':totalStaff')
            ->setValue('latitude',':latitude')
            ->setValue('longitude',':longitude')
            ->setValue('time_created','CURRENT_TIMESTAMP')

            ->setParameter('courtLevelId',$data['courtLevelId'])
            ->setParameter('courtCategoryId',$data['courtCategoryId'])
            ->setParameter('wardId',$data['wardId'])
            ->setParameter('landOwnershipStatusId',$data['landOwnershipStatusId'])
            ->setParameter('landSurveyStatus',$data['landSurveyStatus'])
            ->setParameter('titleDeedStatus',$data['titleDeedStatus'])
            ->setParameter('titleDeedNo',$data['titleDeedNo'])
            ->setParameter('buildingOwnershipStatusId',$data['buildingOwnershipStatusId'])
            ->setParameter('buildingStatusId',$data['buildingStatusId'])
            ->setParameter('functionality',$data['functionality'])
            ->setParameter('ITProvision',$data['ITProvision'])
            ->setParameter('yearConstructed',$data['yearConstructed'])
            ->setParameter('extensionPossibility',$data['extensionPossibility'])
            ->setParameter('casesPerYear',$data['casesPerYear'])
            ->setParameter('populationServed',$data['populationServed'])
            ->setParameter('numberOfJustices',$data['numberOfJustices'])
            ->setParameter('nonJudiciary',$data['nonJudiciary'])
            ->setParameter('totalStaff',$data['totalStaff'])
            ->setParameter('latitude',$data['latitude'])
            ->setParameter('longitude',$data['longitude'])
            ->execute();
        
        $courtId = $conn->lastInsertId();
        
        $transportModes = explode(',',$data['transportModes']);
        $economicActivities = explode(',',$data['economicActivities']);

        foreach ($transportModes as $modeId)
        {
            $this->recordCourtTransportModeDetails($modeId,$courtId);
        }

        foreach ($economicActivities as $activityId)
        {
            $this->recordCourtEconomicActivityDetails($activityId,$courtId);
        }

        
    }

    
    public function recordCourtTransportModeDetails($modeId,$courtId)
    {
        $conn = $this->getEntityManager()->getConnection();

        $queryBuilder = new QueryBuilder($conn);

        $queryBuilder
            ->insert('tbl_court_transport_modes')
            ->setValue('mode_id',':mode_id')
            ->setValue('court_id',':court_id')
            ->setParameter('mode_id',$modeId)
            ->setParameter('court_id',$courtId)
            ->execute();
        
    }

    public function recordCourtEconomicActivityDetails($activityId,$courtId)
    {
        $conn = $this->getEntityManager()->getConnection();

        $queryBuilder = new QueryBuilder($conn);

        $queryBuilder
            ->insert('tbl_court_economic_activities')
            ->setValue('activity_id',':activity_id')
            ->setValue('court_id',':court_id')
            ->setParameter('activity_id',$activityId)
            ->setParameter('court_id',$courtId)
            ->execute();
    }
}
