<?php

namespace AppBundle\Repository\Location;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\ORM\EntityRepository;

class RegionRepository extends EntityRepository
{

    /**
     * @param array $options
     * @return QueryBuilder
     */
    public function findAllRegions($options = [])
    {

        $conn = $this->getEntityManager()->getConnection();

        $queryBuilder = new QueryBuilder($conn);
        $queryBuilder->select('r.region_id,region_name')
            ->from('cfg_regions', 'r');

        $queryBuilder = $this->setFilterOptions($options, $queryBuilder);
        $queryBuilder = $this->setSortOptions($options, $queryBuilder);

        return $queryBuilder;
    }

    public function setFilterOptions($options, QueryBuilder $queryBuilder)
    {
        if (!empty($options['name']))
        {
            return $queryBuilder->andWhere('lower(r.region_name) LIKE lower(:name)')
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
             return $queryBuilder->addOrderBy('region_name', $sortType);
         }

        return $queryBuilder->addOrderBy('r.region_id', 'desc');

    }

    public function countAllRegions(QueryBuilder $queryBuilder)
    {
        return function ($queryBuilder) {
            $queryBuilder->select('COUNT(DISTINCT r.region_id) AS total_results')
                ->groupBy('region_id')
                ->resetQueryPart('orderBy')
                ->resetQueryPart('groupBy')
                ->setMaxResults(1);
        };
    }

    public function getRegionGeometry($options = [])
    {
        $conn = $this->getEntityManager()->getConnection();

        $queryBuilder = new QueryBuilder($conn);

        $queryBuilder->select('region_name,
                               region_code,
                               \'region\' AS level,
                               region_code AS results,
                               ST_AsGeoJSON(ST_Transform(region_geometry,4326))
                               ')
            ->from('spd_regions', 'p');

        return $queryBuilder
            ->execute()
            ->fetchAll();
    }
}
