<?php

namespace AppBundle\Repository\Accounts;

use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\ORM\EntityRepository;

class UserRepository extends EntityRepository
{

    /**
     * @param array $options
     * @return QueryBuilder
     */
    public function findAllStudents($options = [])
    {

        $conn = $this->getEntityManager()->getConnection();

        $queryBuilder = new QueryBuilder($conn);
        $queryBuilder->select('u.id,username,full_name,sex,mobile_phone,account_status')
            ->from('user_accounts', 'u')
            ->join('u', 'tbl_students', 's', 's.student_id=u.student_id')
            ->andWhere('u.student_id IS NOT NULL');

        $queryBuilder = $this->setFilterOptions($options, $queryBuilder);
        $queryBuilder = $this->setSortOptions($options, $queryBuilder);

        return $queryBuilder;
    }

    public function setFilterOptions($options, QueryBuilder $queryBuilder)
    {
        if (!empty($options['username']))
        {
            return $queryBuilder->andwhere('u.username LIKE :username')
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

        return $queryBuilder->addOrderBy('u.id', 'desc');

    }


    public function countAllStudents(QueryBuilder $queryBuilder)
    {
        return function ($queryBuilder) {
            $queryBuilder->select('COUNT(DISTINCT id) AS total_results')
                ->setMaxResults(1);
        };
    }

    /**
     * @param $Id
     * @return QueryBuilder
     */
    public function findStudentById($Id)
    {

        $conn = $this->getEntityManager()->getConnection();

        $queryBuilder = new QueryBuilder($conn);
        $results = $queryBuilder->select('full_name')
            ->from('amis_portal_users', 'u')
            ->where('u.id = :Id')
            ->setParameter('Id', $Id)
            ->execute()
            ->fetch();

        return $results['full_name'];
    }


    /**
     * @param $Id
     * @return QueryBuilder
     */
    public function findStaffByDepartment($Id)
    {

        $conn = $this->getEntityManager()->getConnection();

        $queryBuilder = new QueryBuilder($conn);
        return $queryBuilder->select('id as "value",full_name as "name"')
            ->from('user_accounts', 'u')
            ->join('u','user_roles','r','u.id=r.user_id')
            ->join('r','user_defined_roles','dr','dr.role_id=r.role_id')
            ->where('department_id = :Id')
            ->andWhere('role_name=:name')
            ->setParameter('name','Academic Staff')
            ->setParameter('Id',$Id)
            ->execute()
            ->fetchAll();

    }



    /**
     * @param array $options
     * @return QueryBuilder
     */
    public function findAllStaff($options = [])
    {

        $conn = $this->getEntityManager()->getConnection();

        $queryBuilder = new QueryBuilder($conn);
        $queryBuilder->select('id,username,full_name,mobile_phone,account_status')
            ->from('user_accounts', 'u')
            ->andWhere('u.student_id IS null');
        $queryBuilder = $this->setFilterOptions($options, $queryBuilder);
        $queryBuilder = $this->setSortOptions($options, $queryBuilder);

        return $queryBuilder;
    }

    public function countAllStaff(QueryBuilder $queryBuilder)
    {
        return function ($queryBuilder) {
            $queryBuilder->select('COUNT(DISTINCT id) AS total_results')
                ->setMaxResults(1);
        };
    }
    

}
