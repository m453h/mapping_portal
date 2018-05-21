<?php


namespace AppBundle\Entity\Location;

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
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Location\Zone")
     * @ORM\JoinColumn(name="zone_id", referencedColumnName="zone_id",nullable=true)
     */
    private $zone;



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

    /**
     * @return mixed
     */
    public function getZone()
    {
        return $this->zone;
    }

    /**
     * @param mixed $zone
     */
    public function setZone($zone)
    {
        $this->zone = $zone;
    }


}