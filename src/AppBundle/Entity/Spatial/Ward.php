<?php


namespace AppBundle\Entity\Spatial;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\Location\WardRepository")
 * @ORM\Table(name="cfg_wards")
 */
class Ward
{

    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $wardId;


    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $wardName;


    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $wardCode;



    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Location\District")
     * @ORM\JoinColumn(name="district_id", referencedColumnName="district_id",nullable=false)
     */
    private $district;

    
    /**
     * @return mixed
     */
    public function getDistrict()
    {
        return $this->district;
    }

    /**
     * @param mixed $district
     */
    public function setDistrict($district)
    {
        $this->district = $district;
    }

    /**
     * @return mixed
     */
    public function getWardId()
    {
        return $this->wardId;
    }

    /**
     * @param mixed $wardId
     */
    public function setWardId($wardId)
    {
        $this->wardId = $wardId;
    }

    /**
     * @return mixed
     */
    public function getWardName()
    {
        return $this->wardName;
    }

    /**
     * @param mixed $wardName
     */
    public function setWardName($wardName)
    {
        $this->wardName = $wardName;
    }

    /**
     * @return mixed
     */
    public function getWardCode()
    {
        return $this->wardCode;
    }

    /**
     * @param mixed $wardCode
     */
    public function setWardCode($wardCode)
    {
        $this->wardCode = $wardCode;
    }

}