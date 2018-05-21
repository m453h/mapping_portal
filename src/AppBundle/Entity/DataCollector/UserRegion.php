<?php


namespace AppBundle\Entity\DataCollector;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\AppUsers\UserRegionRepository")
 * @ORM\Table(name="app_users_regions")
 */
class UserRegion
{

    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $userNo;


    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\DataCollector\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="user_id",nullable=false)
     */
    private $user;


    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Location\Region",fetch="EAGER")
     * @ORM\JoinColumn(name="region_id", referencedColumnName="region_id",nullable=true)
     */
    private $region;

    /**
     * @return mixed
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param mixed $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }

    /**
     * @return mixed
     */
    public function getUserNo()
    {
        return $this->userNo;
    }

    /**
     * @param mixed $userNo
     */
    public function setUserNo($userNo)
    {
        $this->userNo = $userNo;
    }

    /**
     * @return mixed
     */
    public function getRegion()
    {
        return $this->region;
    }

    /**
     * @param mixed $region
     */
    public function setRegion($region)
    {
        $this->region = $region;
    }


}