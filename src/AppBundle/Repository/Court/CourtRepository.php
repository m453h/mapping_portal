<?php

namespace AppBundle\Repository\Court;

use Doctrine\Common\Collections\ArrayCollection;
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
        $queryBuilder->select('
        court_id,
        time_created,
        description,
        region_name,
        district_name,
        ward_name,
        first_name,
        surname,
        court_record_status AS status')
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
            return $queryBuilder->andWhere('lower(first_name) LIKE lower(:name) OR lower(surname) LIKE lower(:name) ')
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



    public function findCourtTotalsByRegion()
    {

        $conn = $this->getEntityManager()->getConnection();

        $queryBuilder = new QueryBuilder($conn);
        $results = $queryBuilder->select('region_name AS name,COUNT(court_id) AS total')
            ->from('tbl_court_details', 'c')
            ->join('c','cfg_wards','w','w.ward_id=c.ward_id')
            ->join('w','cfg_districts','d','d.district_id=w.district_id')
            ->join('d','cfg_regions','r','r.region_id=d.region_id')
            ->andWhere('c.court_record_status=:status')
            ->groupBy('r.region_id')
            ->orderBy('total','DESC')
            ->setMaxResults(10)
            ->setParameter('status',true)
            ->execute()
            ->fetchAll();

        $names=[];
        $totals=[];

        $i = 0;

        foreach ($results as $result)
        {
            $names[$i] = '"'.$result['name'].'"';
            $totals[$i] = $result['total'];
            $i++;
        }

        $results = [];

        $results['names'] =  $names;
        $results['totals'] =  $totals;

        return $results;
    }

    public function findCourtTotalByStatus($status)
    {

        $conn = $this->getEntityManager()->getConnection();

        $queryBuilder = new QueryBuilder($conn);

        $queryBuilder->select('COUNT(court_id) AS total')
            ->from('tbl_court_details', 'c')
            ->setMaxResults(1);

        if($status==true)
            $queryBuilder->andWhere('c.court_record_status=:status');
        else
            $queryBuilder->andWhere('c.court_record_status<>:status');


       $result = $queryBuilder->setParameter('status',true)
                ->execute()
                ->fetch();
        
        return $result['total'];
    }


    public function findEconomicActivitiesByCourtId($courtId)
    {

        $conn = $this->getEntityManager()->getConnection();

        $queryBuilder = new QueryBuilder($conn);

        $result = $queryBuilder->select('string_agg(ea.description,\',\') AS description')
            ->from('tbl_court_economic_activities', 'ce')
            ->join('ce','cfg_economic_activities','ea','ea.activity_id=ce.activity_id')
            ->andWhere('court_id=:courtId')
            ->setParameter('courtId',$courtId)
            ->execute()
            ->fetch();

        
        return $result['description'];
    }


    public function findLandUseByCourtId($courtId)
    {

        $conn = $this->getEntityManager()->getConnection();

        $queryBuilder = new QueryBuilder($conn);

        $result = $queryBuilder->select('string_agg(ea.description,\',\') AS description')
            ->from('tbl_court_land_uses', 'ce')
            ->join('ce','cfg_land_uses','ea','ea.activity_id=ce.activity_id')
            ->andWhere('court_id=:courtId')
            ->setParameter('courtId',$courtId)
            ->execute()
            ->fetch();


        return $result['description'];
    }


    public function findTransportModesByCourtId($courtId)
    {

        $conn = $this->getEntityManager()->getConnection();

        $queryBuilder = new QueryBuilder($conn);

        $result = $queryBuilder->select('string_agg(tm.description,\',\') AS description')
            ->from('tbl_court_transport_modes', 'ct')
            ->join('ct','cfg_transport_modes','tm','ct.mode_id=tm.mode_id')
            ->andWhere('court_id=:courtId')
            ->setParameter('courtId',$courtId)
            ->execute()
            ->fetch();


        return $result['description'];
    }


    /**
     * @param $token
     * @return array
     */
    public function findAllIncompleteCourts($token)
    {

        $conn = $this->getEntityManager()->getConnection();

        $queryBuilder = new QueryBuilder($conn);

        return $queryBuilder->select('
        DISTINCT
        court_id AS "courtId",
        court_name AS "courtName",
        ward_name AS "wardName",
        l.description AS "courtLevel"
        ')
            ->from('tbl_court_details', 'c')
            ->leftJoin('c','app_users','u','c.user_id=u.user_id')
            ->join('c','cfg_wards','w','w.ward_id=c.ward_id')
            ->join('c','cfg_court_levels','l','c.level_id=l.level_id')
            ->andWhere('token=:token')
            ->andWhere('court_record_status IS NULL')
            ->andWhere('first_court_view IS NULL AND second_court_view IS NULL AND third_court_view IS NULL AND fourth_court_view IS NULL')
            ->setParameter('token',$token)
            ->addOrderBy('court_id','DESC')
            ->execute()
            ->fetchAll();
    }



    public function findCourtTotalPerCategory()
    {

        $conn = $this->getEntityManager()->getConnection();

        $queryBuilder = new QueryBuilder($conn);

        return $queryBuilder->select('description,COUNT(court_id) AS total')
            ->from('tbl_court_details', 'c')
            ->join('c','cfg_court_levels','l','l.level_id=c.level_id')
            ->andWhere('c.court_record_status=:status')
            ->groupBy('l.description')
            ->orderBy('total','DESC')
            ->setParameter('status',true)
            ->execute()
            ->fetchAll();

    }


    public function findCourtTotalPerRegionPerWard()
    {

        $conn = $this->getEntityManager()->getConnection();

        $queryBuilder = new QueryBuilder($conn);

        return $queryBuilder->select('region_name,district_name,ward_name,COUNT(court_id) AS total')
            ->from('tbl_court_details', 'c')
            ->join('c','cfg_wards','w','w.ward_id=c.ward_id')
            ->join('w','cfg_districts','d','d.district_id=w.district_id')
            ->join('d','cfg_regions','r','r.region_id=d.region_id')
            ->andWhere('c.court_record_status=:status')
            ->groupBy('region_name,district_name,ward_name')
            ->addOrderBy('region_name','ASC')
            ->addOrderBy('district_name','ASC')
            ->addOrderBy('ward_name','ASC')
            ->setParameter('status','1')
            ->execute()
            ->fetchAll();

    }


    public function findCourtTotalPerRegion()
    {

        $conn = $this->getEntityManager()->getConnection();

        $queryBuilder = new QueryBuilder($conn);

        $results =  $queryBuilder->select('region_name,COUNT(DISTINCT(w.ward_id)) AS total')
            ->from('tbl_court_details', 'c')
            ->join('c','cfg_wards','w','w.ward_id=c.ward_id')
            ->join('w','cfg_districts','d','d.district_id=w.district_id')
            ->join('d','cfg_regions','r','r.region_id=d.region_id')
            ->andWhere('c.court_record_status=:status')
            ->groupBy('region_name')
            ->orderBy('region_name','ASC')
            ->setParameter('status',true)
            ->execute()
            ->fetchAll();
        
        $data = [];
        
        foreach ($results as $result)
        {
            $data[$result['region_name']] = $result['total'];
        }
        
        return $data;
    }


    public function findCourtTotalDistricts()
    {

        $conn = $this->getEntityManager()->getConnection();

        $queryBuilder = new QueryBuilder($conn);

        $results =  $queryBuilder->select('district_name,COUNT(DISTINCT(c.ward_id)) AS total')
            ->from('tbl_court_details', 'c')
            ->join('c','cfg_wards','w','w.ward_id=c.ward_id')
            ->join('w','cfg_districts','d','d.district_id=w.district_id')
            ->join('d','cfg_regions','r','r.region_id=d.region_id')
            ->andWhere('c.court_record_status=:status')
            ->groupBy('d.district_id')
            ->orderBy('district_name','ASC')
            ->setParameter('status',true)
            ->execute()
            ->fetchAll();

        $data = [];

        foreach ($results as $result)
        {
            $data[$result['district_name']] = $result['total'];
        }

        return $data;
    }
    
}
