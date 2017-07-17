<?php

namespace AppBundle\Repository\Configuration;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\ORM\EntityRepository;

class EconomicActivityRepository extends EntityRepository
{

    /**
     * @param array $options
     * @return QueryBuilder
     */
    public function findAllEconomicActivities($options = [])
    {

        $conn = $this->getEntityManager()->getConnection();

        $queryBuilder = new QueryBuilder($conn);
        $queryBuilder->select('activity_id,description')
            ->from('cfg_economic_activities', 's');
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

        return $queryBuilder->addOrderBy('activity_id', 'desc');

    }


    public function countAllEconomicActivities(QueryBuilder $queryBuilder)
    {
        return function ($queryBuilder) {
            $queryBuilder->select('COUNT(DISTINCT activity_id) AS total_results')
                ->groupBy('activity_id')
                ->resetQueryPart('orderBy')
                ->resetQueryPart('groupBy')
                ->setMaxResults(1);
        };
    }
    
    public function getAllEconomicActivityKeys()
    {
        $results = $this->findAllEconomicActivities(['sortBy'=>'description','sortType'=>'ASC'])
            ->execute()
            ->fetchAll();

        $data = new ArrayCollection();

        foreach ( $results as $result)
        {
            $data->set($result['description'],$result['activity_id']);
        }
        
        return $data;
    }
    
}
