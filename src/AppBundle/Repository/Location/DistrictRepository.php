<?php

namespace AppBundle\Repository\Location;

use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\ORM\EntityRepository;

class DistrictRepository extends EntityRepository
{



    public function findDistrictIdByName($name)
    {

        $conn = $this->getEntityManager()->getConnection();

        $queryBuilder = new QueryBuilder($conn);
        $queryBuilder->select('district_id as id')
            ->from('cfg_districts', 'd')
            ->andWhere('lower(d.district_name) LIKE lower(:name)')
            ->setParameter('name', strtolower($name));

        $result = $queryBuilder->execute()
            ->fetch();

        return $result['id'];
    }




    /**
     * @param array $options
     * @return QueryBuilder
     */
    public function findAllDistricts($options = [])
    {

        $conn = $this->getEntityManager()->getConnection();

        $queryBuilder = new QueryBuilder($conn);
        $queryBuilder->select('d.district_id,district_name,region_name')
            ->from('cfg_districts', 'd')
            ->join('d','cfg_regions','r','r.region_id=d.region_id');

        $queryBuilder = $this->setFilterOptions($options, $queryBuilder);
        $queryBuilder = $this->setSortOptions($options, $queryBuilder);

        return $queryBuilder;
    }

    public function setFilterOptions($options, QueryBuilder $queryBuilder)
    {
        if (!empty($options['name']))
        {
            return $queryBuilder->andWhere('lower(d.district_name) LIKE lower(:name)')
                ->setParameter('name', '%' . $options['name'] . '%');
        }

        if (!empty($options['userId']))
        {

            return $queryBuilder->join('r','app_users_regions','ar','ar.region_id=r.region_id')
                ->andWhere('user_id=:userId')
                ->setParameter('userId', $options['userId']);
        }

        return $queryBuilder;
    }

    public function setSortOptions($options, QueryBuilder $queryBuilder)
    {

        $options['sortType'] == 'desc' ? $sortType = 'desc' : $sortType = 'asc';

         if ($options['sortBy'] === 'name')
         {
             return $queryBuilder->addOrderBy('district_name', $sortType);
         }

        return $queryBuilder->addOrderBy('d.district_id', 'desc');

    }


    public function countAllDistricts(QueryBuilder $queryBuilder)
    {
        return function ($queryBuilder) {
            $queryBuilder->select('COUNT(DISTINCT d.district_id) AS total_results')
                ->groupBy('district_id')
                ->resetQueryPart('orderBy')
                ->resetQueryPart('groupBy')
                ->setMaxResults(1);
        };
    }



    public function findDistrictsByRegion($regionId)
    {
        $conn = $this->getEntityManager()->getConnection();

        $queryBuilder = new QueryBuilder($conn);

        $queryBuilder->select('district_id as value,district_name AS "name"')
            ->from('cfg_districts', 'd')
            ->where('d.region_id=:regionId')
            ->setParameter('regionId',$regionId);

        return $queryBuilder->execute()->fetchAll();
    }
}
