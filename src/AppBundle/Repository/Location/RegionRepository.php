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

    public function updateRegions()
    {

        $conn = $this->getEntityManager()->getConnection();

        $queryBuilder = new QueryBuilder($conn);
        $results = $queryBuilder->select('r.region_id,r.region_name')
            ->from('cfg_regions', 'r')
            ->execute()
            ->fetchAll();

        $datas = $queryBuilder->select('region_nam,gid as region_c')
            ->from('regions', 'd')
            ->execute()
            ->fetchAll();

        $keys = new ArrayCollection();

        foreach ($datas as $data)
        {
            $keys->set($data['region_nam'],$data['region_c']);
        }

        foreach ($results as $result)
        {

            //dump($result);
            $queryBuilder = new QueryBuilder($conn);

            $code = $keys->get($result['region_name']);

            $queryBuilder->update('cfg_regions')
                ->set('region_code',':region_code')
                ->andWhere('region_name=:region_name')
                ->setParameter('region_code',$code)
                ->setParameter('region_name',$result['region_name'])
                ->execute();
        }


        // dump($keys);

        return $results;
    }

    public function updateDistricts()
    {

        $conn = $this->getEntityManager()->getConnection();

        $queryBuilder = new QueryBuilder($conn);
        $results = $queryBuilder->select('d.district_id,d.district_name')
            ->from('cfg_districts', 'd')
            ->execute()
            ->fetchAll();

        $datas = $queryBuilder->select('district_n,gid as district_c')
            ->from('districts', 'd')
            ->execute()
            ->fetchAll();

        $keys = new ArrayCollection();

        foreach ($datas as $data)
        {
           $keys->set($data['district_n'],$data['district_c']);
        }

        foreach ($results as $result)
        {

            //dump($result);
            $queryBuilder = new QueryBuilder($conn);

            $code = $keys->get($result['district_name']);

            $queryBuilder->update('cfg_districts')
                ->set('district_code',':district_code')
                ->andWhere('district_name=:district_name')
                ->setParameter('district_code',$code)
                ->setParameter('district_name',$result['district_name'])
                ->execute();
        }


       // dump($keys);

        return $results;
    }

    public function updateWards()
    {

        $conn = $this->getEntityManager()->getConnection();

        $queryBuilder = new QueryBuilder($conn);
        $results = $queryBuilder->select('w.ward_id,w.ward_name,d.district_name')
            ->from('cfg_wards', 'w')
            ->join('w','cfg_districts','d','d.district_id=w.district_id')
            ->execute()
            ->fetchAll();

        $queryBuilder = new QueryBuilder($conn);

        $datas = $queryBuilder->select('ward_name,gid as ward_c,district_n')
            ->from('tzwards', 'w')
            ->execute()
            ->fetchAll();

        $keys = new ArrayCollection();

        foreach ($datas as $data)
        {
            $keys->set($data['ward_name'].'|'.$data['district_n'],$data['ward_c']);
        }

        foreach ($results as $result)
        {

            //dump($result);
            $queryBuilder = new QueryBuilder($conn);

            $code = $keys->get($result['ward_name'].'|'.$result['district_name']);

            $queryBuilder->update('cfg_wards')
                ->set('ward_code',':ward_code')
                ->andWhere('ward_name=:ward_name')
                ->setParameter('ward_code',$code)
                ->setParameter('ward_name',$result['ward_name'])
                ->execute();
        }


        // dump($keys);

        return $results;
    }

}
