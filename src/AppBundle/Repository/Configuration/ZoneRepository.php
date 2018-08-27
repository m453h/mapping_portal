<?php

namespace AppBundle\Repository\Configuration;

use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\ORM\EntityRepository;

class ZoneRepository extends EntityRepository
{


    public function findZoneIdByName($name)
    {

        $conn = $this->getEntityManager()->getConnection();

        $queryBuilder = new QueryBuilder($conn);
        $queryBuilder->select('zone_id AS id')
            ->from('cfg_zones', 'd')
            ->andWhere('lower(d.zone_name) LIKE lower(:name)')
            ->setParameter('name', strtolower($name));

        $result = $queryBuilder->execute()
            ->fetch();

        return $result['id'];
    }


    /**
     * @param array $options
     * @return QueryBuilder
     */
    public function findAllZones($options = [])
    {

        $conn = $this->getEntityManager()->getConnection();

        $queryBuilder = new QueryBuilder($conn);
        $queryBuilder->select('zone_id,zone_name')
            ->from('cfg_zones', 'z');
        $queryBuilder = $this->setFilterOptions($options, $queryBuilder);
        $queryBuilder = $this->setSortOptions($options, $queryBuilder);

        return $queryBuilder;
    }

    public function setFilterOptions($options, QueryBuilder $queryBuilder)
    {
        if (!empty($options['name']))
        {
            return $queryBuilder->andWhere('lower(zone_name) LIKE lower(:name)')
                ->setParameter('name', '%' . $options['name'] . '%');
        }

        return $queryBuilder;
    }

    public function setSortOptions($options, QueryBuilder $queryBuilder)
    {

        $options['sortType'] == 'desc' ? $sortType = 'desc' : $sortType = 'asc';

         if ($options['sortBy'] === 'name')
         {
             return $queryBuilder->addOrderBy('zone_name', $sortType);
         }

        return $queryBuilder->addOrderBy('zone_id', 'desc');

    }


    public function countAllZones(QueryBuilder $queryBuilder)
    {
        return function ($queryBuilder) {
            $queryBuilder->select('COUNT(DISTINCT zone_id) AS total_results')
                ->groupBy('zone_id')
                ->resetQueryPart('orderBy')
                ->resetQueryPart('groupBy')
                ->setMaxResults(1);
        };
    }
    
}
