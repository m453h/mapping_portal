<?php


namespace AppBundle\Entity\Location;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\Location\StreetVillageRepository")
 * @ORM\Table(name="cfg_villages_streets")
 */
class VillageStreet
{

    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $areaId;


    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $areaName;
    

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Location\Ward")
     * @ORM\JoinColumn(name="ward_id", referencedColumnName="ward_id",nullable=false)
     */
    private $ward;


    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $isVillage;

    
    /**
     * @return mixed
     */
    public function getWard()
    {
        return $this->ward;
    }

    /**
     * @param mixed $ward
     */
    public function setWard($ward)
    {
        $this->ward = $ward;
    }

    /**
     * @return mixed
     */
    public function getAreaId()
    {
        return $this->areaId;
    }

    /**
     * @param mixed $areaId
     */
    public function setAreaId($areaId)
    {
        $this->areaId = $areaId;
    }

    /**
     * @return mixed
     */
    public function getAreaName()
    {
        return $this->areaName;
    }

    /**
     * @param mixed $areaName
     */
    public function setAreaName($areaName)
    {
        $this->areaName = $areaName;
    }

    /**
     * @return mixed
     */
    public function getIsVillage()
    {
        return $this->isVillage;
    }

    /**
     * @param mixed $isVillage
     */
    public function setIsVillage($isVillage)
    {
        $this->isVillage = $isVillage;
    }


}