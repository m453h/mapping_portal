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
        $queryBuilder->select('court_id,time_created,description,region_name,district_name,ward_name,first_name,surname')
            ->from('tbl_court_details', 'c')
            ->join('c','cfg_wards','w','w.ward_id=c.ward_id')
            ->join('w','cfg_districts','d','d.district_id=w.district_id')
            ->join('d','cfg_regions','r','d.region_id=r.region_id')
            ->join('c','cfg_court_levels','l','l.level_id=c.level_id')
            ->leftJoin('c','app_users','u','u.user_id=c.user_id');
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

        return $queryBuilder->addOrderBy('court_id', 'desc');

    }

    public function countAllCourts(QueryBuilder $queryBuilder)
    {
        return function ($queryBuilder) {
            $queryBuilder->select('COUNT(DISTINCT court_id) AS total_results')
                ->groupBy('court_id')
                ->resetQueryPart('orderBy')
                ->resetQueryPart('groupBy')
                ->setMaxResults(1);
        };
    }


    /**
     * @param $data
     * @return string
     */
    public function recordCourtDetails($data)
    {
        $conn = $this->getEntityManager()->getConnection();

        $queryBuilder = new QueryBuilder($conn);

        $queryBuilder
            ->insert('tbl_court_details')
            ->setValue('level_id',':courtLevelId')
            ->setValue('ward_id',':wardId')
            ->setValue('land_ownership_status_id',':landOwnershipStatusId')
            ->setValue('is_land_surveyed',':landSurveyStatus')
            ->setValue('has_title_deed',':titleDeedStatus')
            ->setValue('title_deed',':titleDeedNo')
            ->setValue('plot_number',':plotNo')
            ->setValue('building_ownership_status_id',':buildingOwnershipStatusId')
            ->setValue('court_building_status_id',':buildingStatusId')
            ->setValue('has_extension_possibility',':extensionPossibility')
            ->setValue('year_constructed',':yearConstructed')
            ->setValue('meets_functionality',':functionality')
            ->setValue('has_last_mile_connectivity',':lastMileConnectivity')
            ->setValue('number_of_computers',':numberOfComputers')
            ->setValue('internet_availability',':internetAvailability')
            ->setValue('bandwidth',':bandwidth')
            ->setValue('available_systems',':availableSystems')
            ->setValue('cases_per_year',':casesPerYear')
            ->setValue('population_served',':populationServed')
            ->setValue('number_of_Justices',':numberOfJustices')
            ->setValue('number_of_judges',':judges')
            ->setValue('number_of_resident_magistrates',':residentMagistrates')
            ->setValue('number_of_district_magistrates',':districtMagistrates')
            ->setValue('number_of_magistrates',':magistrates')
            ->setValue('number_of_court_clerks',':courtClerks')
            ->setValue('number_of_non_judiciary_staff',':nonJudiciary')
            ->setValue('environmental_status',':environmentalStatusId')
            ->setValue('court_coordinates_dms',':DMSCourtCoordinates')
            ->setValue('court_latitude',':DECCourtLatitude')
            ->setValue('court_longitude',':DECCourtLongitude')
            ->setValue('last_mile_connectivity_dms',':DMSConnectivityCoordinates')
            ->setValue('last_mile_connectivity_latitude',':DECConnectivityLatitude')
            ->setValue('last_mile_connectivity_longitude',':DECConnectivityLongitude')
            ->setValue('fibre_distance',':fibreDistance')
            ->setValue('areas_entitled',':areasEntitled')
            ->setValue('time_created','CURRENT_TIMESTAMP')
            ->setValue('unique_court_id',':uniqueCourtId')
            ->setValue('user_id',':userId')



            ->setParameter('courtLevelId',$data['courtLevelId'])
            ->setParameter('wardId',$data['wardId'])
            ->setParameter('landOwnershipStatusId',$data['landOwnershipStatusId'])
            ->setParameter('landSurveyStatus',$data['landSurveyStatus'])
            ->setParameter('titleDeedStatus',$data['titleDeedStatus'])
            ->setParameter('titleDeedNo',$data['titleDeedNo'])
            ->setParameter('plotNo',$data['plotNo'])
            ->setParameter('buildingOwnershipStatusId',$data['buildingOwnershipStatusId'])
            ->setParameter('buildingStatusId',$data['buildingStatusId'])
            ->setParameter('extensionPossibility',$data['extensionPossibility'])
            ->setParameter('yearConstructed',$data['yearConstructed'])
            ->setParameter('functionality',$data['functionality'])
            ->setParameter('lastMileConnectivity',$data['lastMileConnectivity'])
            ->setParameter('numberOfComputers',$data['numberOfComputers'])
            ->setParameter('internetAvailability',$data['internetAvailability'])
            ->setParameter('bandwidth',$data['bandwidth'])
            ->setParameter('availableSystems',$data['availableSystems'])
            ->setParameter('casesPerYear',$data['casesPerYear'])
            ->setParameter('populationServed',$data['populationServed'])
            ->setParameter('numberOfJustices',$data['numberOfJustices'])
            ->setParameter('judges',$data['judges'])
            ->setParameter('residentMagistrates',$data['residentMagistrates'])
            ->setParameter('districtMagistrates',$data['districtMagistrates'])
            ->setParameter('magistrates',$data['magistrates'])
            ->setParameter('courtClerks',$data['courtClerks'])
            ->setParameter('nonJudiciary',$data['nonJudiciary'])
            ->setParameter('environmentalStatusId',$data['environmentalStatusId'])
            ->setParameter('DMSCourtCoordinates',$data['DMSCourtCoordinates'])
            ->setParameter('DECCourtLatitude',$data['DECCourtLatitude'])
            ->setParameter('DECCourtLongitude',$data['DECCourtLongitude'])
            ->setParameter('DMSConnectivityCoordinates',$data['DMSConnectivityCoordinates'])
            ->setParameter('DECConnectivityLatitude',$data['DECConnectivityLatitude'])
            ->setParameter('DECConnectivityLongitude',$data['DECConnectivityLongitude'])
            ->setParameter('fibreDistance',$data['fibreDistance'])
            ->setParameter('areasEntitled',$data['areasEntitled'])
            ->setParameter('uniqueCourtId',$data['uniqueCourtId'])
            ->setParameter('userId',$data['userId'])
            ->execute();
        
        $courtId = $conn->lastInsertId();
        
        $transportModes = explode(',',$data['transportModes']);
        $economicActivities = explode(',',$data['economicActivities']);
        $landUses = explode(',',$data['landUses']);

        foreach ($transportModes as $modeId)
        {
            $this->recordCourtTransportModeDetails($modeId,$courtId);
        }

        foreach ($economicActivities as $activityId)
        {
            $this->recordCourtEconomicActivityDetails($activityId,$courtId);
        }

        foreach ($landUses as $activityId)
        {
            $this->recordCourtLandUseDetails($activityId,$courtId);
        }

        return $courtId;
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


    public function recordCourtLandUseDetails($activityId,$courtId)
    {
        $conn = $this->getEntityManager()->getConnection();

        $queryBuilder = new QueryBuilder($conn);

        $queryBuilder
            ->insert('tbl_court_land_uses')
            ->setValue('activity_id',':activity_id')
            ->setValue('court_id',':court_id')
            ->setParameter('activity_id',$activityId)
            ->setParameter('court_id',$courtId)
            ->execute();
    }


    /**
     * @param $data
     * @param $courtId
     * @return string
     */
    public function updateCourtDetails($data,$courtId)
    {
        $conn = $this->getEntityManager()->getConnection();

        $queryBuilder = new QueryBuilder($conn);

        $queryBuilder
            ->update('tbl_court_details')
            ->set('first_court_view',':first')
            ->set('second_court_view',':second')
            ->set('third_court_view',':third')
            ->set('fourth_court_view',':fourth')
            ->where('court_id = :courtId')
            ->setParameter('first',$data['first'])
            ->setParameter('second',$data['second'])
            ->setParameter('third',$data['third'])
            ->setParameter('fourth',$data['fourth'])
            ->setParameter('courtId',$courtId)
            ->execute();
        
    }


    /**
     * @param $courtId
     * @return QueryBuilder
     */
    public function findCourtDetails($courtId)
    {

        $conn = $this->getEntityManager()->getConnection();

        $queryBuilder = new QueryBuilder($conn);
        $data = $queryBuilder->select('court_id,
        time_created AS "timeCreated",
        description AS "courtLevel",
        region_name AS "regionName",
        district_name AS "districtName",
        ward_name AS "wardName",
        first_name AS "firstName",
        surname AS "surname",
        land_o
        ')
            ->from('tbl_court_details', 'c')
            ->join('c','cfg_wards','w','w.ward_id=c.ward_id')
            ->join('w','cfg_districts','d','d.district_id=w.district_id')
            ->join('d','cfg_regions','r','d.region_id=r.region_id')
            ->join('c','cfg_court_levels','l','l.level_id=c.level_id')
            ->leftJoin('c','app_users','u','u.user_id=c.user_id')
            ->execute()
            ->fetch();

        return $data;
    }



}
