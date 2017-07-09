<?php

namespace AppBundle\Repository\AppUsers;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\ORM\EntityRepository;

class UserRegionRepository extends EntityRepository
{


    /**
     * @param array $options
     * @return QueryBuilder
     */
    public function findAllUserRegions($options=[])
    {

        $conn = $this->getEntityManager()->getConnection();

        $queryBuilder = new QueryBuilder($conn);
        $queryBuilder->select('user_no,r.region_name')
            ->from('cfg_regions', 'r')
            ->join('r','app_users_regions','ur','ur.region_id=r.region_id');

        $queryBuilder = $this->setFilterOptions($options,$queryBuilder);
        $queryBuilder = $this->setSortOptions($options,$queryBuilder);

        return $queryBuilder;
    }

    public function setFilterOptions($options, QueryBuilder $queryBuilder)
    {

        if(!empty($options['regionId']))
        {
            $queryBuilder->andWhere('ur.region_id = :regionId')
                ->setParameter('region_id',$options['regionId']);
        }


        return $queryBuilder;
    }

    public function setSortOptions($options, QueryBuilder $queryBuilder)
    {
        $options['sortType'] == 'desc' ? $sortType='desc': $sortType='asc';

        if($options['sortBy'] === 'name')
        {
            $queryBuilder->addOrderBy('region_name',$sortType);
        }
        else
        {
            $queryBuilder->addOrderBy('user_no','DESC');
        }

        return $queryBuilder;
    }

    public function countAllUserRegions(QueryBuilder $queryBuilder)
    {
        return function ($queryBuilder) {
            $queryBuilder->select('COUNT(DISTINCT user_no) AS total_results')
                ->groupBy('user_no')
                ->resetQueryPart('orderBy')
                ->resetQueryPart('groupBy')
                ->setMaxResults(1);
        };
    }

    public function recordUserRegion($regionId,$userId)
    {
        $conn = $this->getEntityManager()->getConnection();

        $queryBuilder = new QueryBuilder($conn);
        $queryBuilder->insert('app_users_regions')
            ->setValue('region_id','?')
            ->setValue('user_id','?')
            ->setValue('user_no','?')
            ->setParameter(0,$regionId)
            ->setParameter(1,$userId);
        $queryBuilder->execute();
    }

    public function deleteUserRegions($userId)
    {
        $conn = $this->getEntityManager()->getConnection();

        $queryBuilder = new QueryBuilder($conn);
        $queryBuilder->delete('app_users_regions')
            ->andWhere('user_id=?')
            ->setParameter(0,$userId);
        $queryBuilder->execute();
    }


    public function getAssignedRegionsToUser($userId)
    {
        $conn = $this->getEntityManager()->getConnection();

        $queryBuilder = new QueryBuilder($conn);

        $results = $queryBuilder->select('r.region_id')
            ->from('app_users_regions', 'r')
            ->andWhere('user_id=:user_id')
            ->setParameter('user_id',$userId);

        $results = $results->execute()
            ->fetchAll();

        $myArray = [];

        foreach ($results as $data)
        {
            array_push($myArray,$data['region_id']);
        }

        return $myArray;
    }

    public function getAvailableRegions()
    {
        $conn = $this->getEntityManager()->getConnection();

        $queryBuilder = new QueryBuilder($conn);

        $queryBuilder->select('r.region_id AS "value", r.region_name AS "name"')
            ->from('cfg_regions', 'r');

        $results = $queryBuilder ->execute()
            ->fetchAll();

        $myArray = [];

        foreach ($results as $data)
        {
            $myArray[$data['name']]=$data['value'];
        }

        return $myArray;
    }


}
