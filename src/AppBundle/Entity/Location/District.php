<?php


namespace AppBundle\Entity\Location;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\Location\DistrictRepository")
 * @ORM\Table(name="cfg_districts")
 */
class District
{

    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $districtId;


    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $districtName;
    

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Location\Region")
     * @ORM\JoinColumn(name="region_id", referencedColumnName="region_id",nullable=false)
     */
    private $region;

    
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

    /**
     * @return mixed
     */
    public function getDistrictId()
    {
        return $this->districtId;
    }

    /**
     * @param mixed $districtId
     */
    public function setDistrictId($districtId)
    {
        $this->districtId = $districtId;
    }

    /**
     * @return mixed
     */
    public function getDistrictName()
    {
        return $this->districtName;
    }

    /**
     * @param mixed $districtName
     */
    public function setDistrictName($districtName)
    {
        $this->districtName = $districtName;
    }

    
}