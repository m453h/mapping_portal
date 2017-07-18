<?php

namespace AppBundle\Repository\AppUsers;

use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\ORM\EntityRepository;

class UserRepository extends EntityRepository
{

    /**
     * @param array $options
     * @return QueryBuilder
     */
    public function findAllAppUsers($options = [])
    {

        $conn = $this->getEntityManager()->getConnection();

        $queryBuilder = new QueryBuilder($conn);
        $queryBuilder->select('user_id,username,first_name,surname,mobile')
            ->from('app_users', 'u');

        $queryBuilder = $this->setFilterOptions($options, $queryBuilder);
        $queryBuilder = $this->setSortOptions($options, $queryBuilder);

        return $queryBuilder;
    }

    public function setFilterOptions($options, QueryBuilder $queryBuilder)
    {
        if (!empty($options['username']))
        {
            return $queryBuilder->andWhere('lower(u.username) LIKE lower(:username)')
                ->setParameter('username', '%' . $options['username'] . '%');
        }

        return $queryBuilder;
    }

    public function setSortOptions($options, QueryBuilder $queryBuilder)
    {

        $options['sortType'] == 'desc' ? $sortType = 'desc' : $sortType = 'asc';

         if ($options['sortBy'] === 'username')
         {
             return $queryBuilder->addOrderBy('u.username', $sortType);
         }

        return $queryBuilder->addOrderBy('u.user_id', 'desc');

    }


    public function countAllAppUsers(QueryBuilder $queryBuilder)
    {
        return function ($queryBuilder) {
            $queryBuilder->select('COUNT(DISTINCT user_id) AS total_results')
                ->groupBy('user_id')
                ->resetQueryPart('orderBy')
                ->resetQueryPart('groupBy')
                ->setMaxResults(1);
        };
    }


    /**
     * @return QueryBuilder
     */
    public function findLatestDownloadVersion()
    {

        $conn = $this->getEntityManager()->getConnection();

        $queryBuilder = new QueryBuilder($conn);
        $result = $queryBuilder->select('version_id AS "versionId"')
            ->from('app_data_versions', 'v')
            ->orderBy('version_id','DESC')
            ->setMaxResults(1)
            ->execute()
            ->fetch();


        return $result;
    }




}
