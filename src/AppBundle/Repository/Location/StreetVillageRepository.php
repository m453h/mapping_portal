<?php

namespace AppBundle\Repository\Location;

use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\ORM\EntityRepository;

class StreetVillageRepository extends EntityRepository
{

    /**
     * @param array $options
     * @return QueryBuilder
     */
    public function findAllAreas($options = [])
    {

        $conn = $this->getEntityManager()->getConnection();

        $queryBuilder = new QueryBuilder($conn);
        $queryBuilder->select('area_id,area_name,ward_name,district_name,region_name')
            ->from('cfg_villages_streets', 'v')
            ->join('v','cfg_wards','w','w.ward_id=v.ward_id')
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
            return $queryBuilder->andWhere('lower(r.area_name) LIKE lower(:name)')
                ->setParameter('name', '%' . $options['name'] . '%');
        }

        return $queryBuilder;
    }

    public function setSortOptions($options, QueryBuilder $queryBuilder)
    {

        $options['sortType'] == 'desc' ? $sortType = 'desc' : $sortType = 'asc';

         if ($options['sortBy'] === 'name')
         {
             return $queryBuilder->addOrderBy('area_name', $sortType);
         }

        return $queryBuilder->addOrderBy('v.area_id', 'desc');

    }


    public function countAllAreas(QueryBuilder $queryBuilder)
    {
        return function ($queryBuilder) {
            $queryBuilder->select('COUNT(DISTINCT area_id) AS total_results')
                ->groupBy('area_id')
                ->resetQueryPart('orderBy')
                ->resetQueryPart('groupBy')
                ->setMaxResults(1);
        };
    }
    
}
