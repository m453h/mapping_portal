<?php


namespace AppBundle\Entity\Spatial;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\Location\RegionRepository")
 * @ORM\Table(name="cfg_regions")
 */
class Region
{

    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $regionId;


    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $regionName;


    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $regionStatus;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $regionCode;
    
    
    /**
     * @return mixed
     */
    public function getRegionName()
    {
        return $this->regionName;
    }

    /**
     * @param mixed $regionName
     */
    public function setRegionName($regionName)
    {
        $this->regionName = $regionName;
    }

    /**
     * @return mixed
     */
    public function getRegionId()
    {
        return $this->regionId;
    }

    /**
     * @param mixed $regionId
     */
    public function setRegionId($regionId)
    {
        $this->regionId = $regionId;
    }

    /**
     * @return mixed
     */
    public function getRegionStatus()
    {
        return $this->regionStatus;
    }

    /**
     * @param mixed $regionStatus
     */
    public function setRegionStatus($regionStatus)
    {
        $this->regionStatus = $regionStatus;
    }

    /**
     * @return mixed
     */
    public function getRegionCode()
    {
        return $this->regionCode;
    }

    /**
     * @param mixed $regionCode
     */
    public function setRegionCode($regionCode)
    {
        $this->regionCode = $regionCode;
    }



}