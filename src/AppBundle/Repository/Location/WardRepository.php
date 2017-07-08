<?php

namespace AppBundle\Repository\Location;

use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\ORM\EntityRepository;

class WardRepository extends EntityRepository
{

    /**
     * @param array $options
     * @return QueryBuilder
     */
    public function findAllWards($options = [])
    {

        $conn = $this->getEntityManager()->getConnection();

        $queryBuilder = new QueryBuilder($conn);
        $queryBuilder->select('ward_id,ward_name,district_name,region_name')
            ->from('cfg_wards', 'w')
            ->join('w','cfg_districts','d','d.district_id=w.district_id')
            ->join('d','cfg_regions','r','r.region_id=d.region_id');

        $queryBuilder = $this->setFilterOptions($options, $queryBuilder);
        $queryBuilder = $this->setSortOptions($options, $queryBuilder);

        return $queryBuilder;
    }

    public function setFilterOptions($options, QueryBuilder $queryBuilder)
    {
        if (!empty($options['name']))
        {
            return $queryBuilder->andWhere('lower(r.ward_name) LIKE lower(:name)')
                ->setParameter('name', '%' . $options['name'] . '%');
        }

        return $queryBuilder;
    }

    public function setSortOptions($options, QueryBuilder $queryBuilder)
    {

        $options['sortType'] == 'desc' ? $sortType = 'desc' : $sortType = 'asc';

         if ($options['sortBy'] === 'name')
         {
             return $queryBuilder->addOrderBy('ward_name', $sortType);
         }

        return $queryBuilder->addOrderBy('w.ward_id', 'desc');

    }


    public function countAllWards(QueryBuilder $queryBuilder)
    {
        return function ($queryBuilder) {
            $queryBuilder->select('COUNT(DISTINCT ward_id) AS total_results')
                ->groupBy('ward_id')
                ->resetQueryPart('orderBy')
                ->resetQueryPart('groupBy')
                ->setMaxResults(1);
        };
    }


    public function findWardsByDistrict($districtId)
    {
        $conn = $this->getEntityManager()->getConnection();

        $queryBuilder = new QueryBuilder($conn);

        $queryBuilder->select('ward_id as value,ward_name AS "name"')
            ->from('cfg_wards', 'w')
            ->where('district_id=:districtId')
            ->setParameter('districtId',$districtId);

        return $queryBuilder->execute()->fetchAll();
    }
    
}
