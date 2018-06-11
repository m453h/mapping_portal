<?php


namespace AppBundle\Entity\Spatial;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\Location\RegionRepository")
 * @ORM\Table(name="spd_regions",uniqueConstraints={@ORM\UniqueConstraint(name="unique_region_code", columns={"region_code"})})
 */
class Region
{

    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $gid;

    /**
     * @ORM\Column(type="string")
     */
    private $regionCode;
    

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $regionName;

    
    /**
     * @ORM\Column(type="geometry", nullable=true)
     */
    private $regionGeometry;


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
    public function getRegionGeometry()
    {
        return $this->regionGeometry;
    }

    /**
     * @param mixed $regionGeometry
     */
    public function setRegionGeometry($regionGeometry)
    {
        $this->regionGeometry = $regionGeometry;
    }

    /**
     * @return mixed
     */
    public function getGid()
    {
        return $this->gid;
    }

    /**
     * @param mixed $gid
     */
    public function setGid($gid)
    {
        $this->gid = $gid;
    }


    
}