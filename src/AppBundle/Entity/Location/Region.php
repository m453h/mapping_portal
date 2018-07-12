<?php


namespace AppBundle\Entity\Location;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Form\Validator\Constraints as CourtMappingAssert;


use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\Location\RegionRepository")
 * @ORM\Table(name="cfg_regions",uniqueConstraints={@ORM\UniqueConstraint(name="unique_region_name", columns={"region_name"})})
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
     * @Assert\NotBlank()
     * @CourtMappingAssert\IsUniqueRegion()
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
     * @Assert\NotBlank()
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