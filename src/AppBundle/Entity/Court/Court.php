<?php


namespace AppBundle\Entity\Court;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\Court\CourtRepository")
 * @ORM\Table(name="tbl_court_details")
 */
class Court
{

    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer",options={"default"="nextval('tbl_court_details_court_id_seq')"})
     */
    private $courtId;


    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Configuration\CourtLevel")
     * @ORM\JoinColumn(name="level_id", referencedColumnName="level_id",nullable=false)
     */
    private $courtLevel;


    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Configuration\CourtCategory")
     * @ORM\JoinColumn(name="category_id", referencedColumnName="category_id",nullable=false)
     */
    private $courtCategory;


    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Location\Ward")
     * @ORM\JoinColumn(name="ward_id", referencedColumnName="ward_id",nullable=false)
     */
    private $ward;


    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Configuration\LandOwnerShipStatus")
     * @ORM\JoinColumn(name="land_ownership_status_id", referencedColumnName="status_id",nullable=false)
     */
    private $landOwnershipStatus;
    
    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $isLandSurveyed;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $hasTitleDeed;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $titleDeed;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Configuration\CourtBuildingOwnershipStatus")
     * @ORM\JoinColumn(name="building_ownership_status_id", referencedColumnName="status_id",nullable=false)
     */
    private $buildingOwnershipStatus;


    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Configuration\CourtBuildingStatus")
     * @ORM\JoinColumn(name="court_building_status_id", referencedColumnName="status_id",nullable=false)
     */
    private $buildingStatus;


    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $functionality;


    /**
     * @ORM\Column(type="string", nullable=true,name="it_provision")
     */
    private $ITProvision;


    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $yearConstructed;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $hasExtensionPossibility;


    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $casesPerYear;


    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $populationServed;


    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $numberOfJustices;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $nonJudiciary;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $totalStaff;


    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $latitude;


    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $longitude;


    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\AppUsers\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="user_id",nullable=true)
     */
    private $createdBy;



    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $timeCreated;


    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $firstCourtView;


    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $secondCourtView;


    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $thirdCourtView;


    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $uniqueCourtId;





    /**
     * @return mixed
     */
    public function getCourtLevel()
    {
        return $this->courtLevel;
    }

    /**
     * @param mixed $courtLevel
     */
    public function setCourtLevel($courtLevel)
    {
        $this->courtLevel = $courtLevel;
    }

    /**
     * @return mixed
     */
    public function getCourtId()
    {
        return $this->courtId;
    }

    /**
     * @param mixed $courtId
     */
    public function setCourtId($courtId)
    {
        $this->courtId = $courtId;
    }

    /**
     * @return mixed
     */
    public function getCourtCategory()
    {
        return $this->courtCategory;
    }

    /**
     * @param mixed $courtCategory
     */
    public function setCourtCategory($courtCategory)
    {
        $this->courtCategory = $courtCategory;
    }

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
    public function getLandOwnershipStatus()
    {
        return $this->landOwnershipStatus;
    }

    /**
     * @param mixed $landOwnershipStatus
     */
    public function setLandOwnershipStatus($landOwnershipStatus)
    {
        $this->landOwnershipStatus = $landOwnershipStatus;
    }

    /**
     * @return mixed
     */
    public function getIsLandSurveyed()
    {
        return $this->isLandSurveyed;
    }

    /**
     * @param mixed $isLandSurveyed
     */
    public function setIsLandSurveyed($isLandSurveyed)
    {
        $this->isLandSurveyed = $isLandSurveyed;
    }

    /**
     * @return mixed
     */
    public function getHasTitleDeed()
    {
        return $this->hasTitleDeed;
    }

    /**
     * @param mixed $hasTitleDeed
     */
    public function setHasTitleDeed($hasTitleDeed)
    {
        $this->hasTitleDeed = $hasTitleDeed;
    }

    /**
     * @return mixed
     */
    public function getBuildingOwnershipStatus()
    {
        return $this->buildingOwnershipStatus;
    }

    /**
     * @param mixed $buildingOwnershipStatus
     */
    public function setBuildingOwnershipStatus($buildingOwnershipStatus)
    {
        $this->buildingOwnershipStatus = $buildingOwnershipStatus;
    }

    /**
     * @return mixed
     */
    public function getBuildingStatus()
    {
        return $this->buildingStatus;
    }

    /**
     * @param mixed $buildingStatus
     */
    public function setBuildingStatus($buildingStatus)
    {
        $this->buildingStatus = $buildingStatus;
    }

    /**
     * @return mixed
     */
    public function getFunctionality()
    {
        return $this->functionality;
    }

    /**
     * @param mixed $functionality
     */
    public function setFunctionality($functionality)
    {
        $this->functionality = $functionality;
    }

    /**
     * @return mixed
     */
    public function getITProvision()
    {
        return $this->ITProvision;
    }

    /**
     * @param mixed $ITProvision
     */
    public function setITProvision($ITProvision)
    {
        $this->ITProvision = $ITProvision;
    }

    /**
     * @return mixed
     */
    public function getYearConstructed()
    {
        return $this->yearConstructed;
    }

    /**
     * @param mixed $yearConstructed
     */
    public function setYearConstructed($yearConstructed)
    {
        $this->yearConstructed = $yearConstructed;
    }

    /**
     * @return mixed
     */
    public function getHasExtensionPossibility()
    {
        return $this->hasExtensionPossibility;
    }

    /**
     * @param mixed $hasExtensionPossibility
     */
    public function setHasExtensionPossibility($hasExtensionPossibility)
    {
        $this->hasExtensionPossibility = $hasExtensionPossibility;
    }

    /**
     * @return mixed
     */
    public function getCasesPerYear()
    {
        return $this->casesPerYear;
    }

    /**
     * @param mixed $casesPerYear
     */
    public function setCasesPerYear($casesPerYear)
    {
        $this->casesPerYear = $casesPerYear;
    }

    /**
     * @return mixed
     */
    public function getPopulationServed()
    {
        return $this->populationServed;
    }

    /**
     * @param mixed $populationServed
     */
    public function setPopulationServed($populationServed)
    {
        $this->populationServed = $populationServed;
    }

    /**
     * @return mixed
     */
    public function getNumberOfJustices()
    {
        return $this->numberOfJustices;
    }

    /**
     * @param mixed $numberOfJustices
     */
    public function setNumberOfJustices($numberOfJustices)
    {
        $this->numberOfJustices = $numberOfJustices;
    }

    /**
     * @return mixed
     */
    public function getNonJudiciary()
    {
        return $this->nonJudiciary;
    }

    /**
     * @param mixed $nonJudiciary
     */
    public function setNonJudiciary($nonJudiciary)
    {
        $this->nonJudiciary = $nonJudiciary;
    }

    /**
     * @return mixed
     */
    public function getTotalStaff()
    {
        return $this->totalStaff;
    }

    /**
     * @param mixed $totalStaff
     */
    public function setTotalStaff($totalStaff)
    {
        $this->totalStaff = $totalStaff;
    }

    /**
     * @return mixed
     */
    public function getLatitude()
    {
        return $this->latitude;
    }

    /**
     * @param mixed $latitude
     */
    public function setLatitude($latitude)
    {
        $this->latitude = $latitude;
    }

    /**
     * @return mixed
     */
    public function getLongitude()
    {
        return $this->longitude;
    }

    /**
     * @param mixed $longitude
     */
    public function setLongitude($longitude)
    {
        $this->longitude = $longitude;
    }

    /**
     * @return mixed
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    /**
     * @param mixed $createdBy
     */
    public function setCreatedBy($createdBy)
    {
        $this->createdBy = $createdBy;
    }

    /**
     * @return mixed
     */
    public function getTimeCreated()
    {
        return $this->timeCreated;
    }

    /**
     * @param mixed $timeCreated
     */
    public function setTimeCreated($timeCreated)
    {
        $this->timeCreated = $timeCreated;
    }

    /**
     * @return mixed
     */
    public function getTitleDeed()
    {
        return $this->titleDeed;
    }

    /**
     * @param mixed $titleDeed
     */
    public function setTitleDeed($titleDeed)
    {
        $this->titleDeed = $titleDeed;
    }

    /**
     * @return mixed
     */
    public function getFirstCourtView()
    {
        return $this->firstCourtView;
    }

    /**
     * @param mixed $firstCourtView
     */
    public function setFirstCourtView($firstCourtView)
    {
        $this->firstCourtView = $firstCourtView;
    }

    /**
     * @return mixed
     */
    public function getSecondCourtView()
    {
        return $this->secondCourtView;
    }

    /**
     * @param mixed $secondCourtView
     */
    public function setSecondCourtView($secondCourtView)
    {
        $this->secondCourtView = $secondCourtView;
    }

    /**
     * @return mixed
     */
    public function getThirdCourtView()
    {
        return $this->thirdCourtView;
    }

    /**
     * @param mixed $thirdCourtView
     */
    public function setThirdCourtView($thirdCourtView)
    {
        $this->thirdCourtView = $thirdCourtView;
    }

    /**
     * @return mixed
     */
    public function getUniqueCourtId()
    {
        return $this->uniqueCourtId;
    }

    /**
     * @param mixed $uniqueCourtId
     */
    public function setUniqueCourtId($uniqueCourtId)
    {
        $this->uniqueCourtId = $uniqueCourtId;
    }


}