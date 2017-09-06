<?php


namespace AppBundle\Entity\Court;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\Court\CourtRepository")
 * @ORM\Table(name="tbl_court_details",uniqueConstraints={@ORM\UniqueConstraint(name="court_details", columns={"unique_court_id","user_id"})})
 */
class Court
{

    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $courtId;


    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Configuration\CourtLevel")
     * @ORM\JoinColumn(name="level_id", referencedColumnName="level_id",nullable=false)
     */
    private $courtLevel;

    
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
     * @ORM\Column(type="string", nullable=true)
     */
    private $plotNumber;


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
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $hasExtensionPossibility;


    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $yearConstructed;


    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $meetsFunctionality;


    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $hasLastMileConnectivity;


    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $numberOfComputers;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $internetAvailability;


    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $bandwidth;



    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $availableSystems;


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
    private $numberOfJudges;


    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $numberOfResidentMagistrates;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $numberOfDistrictMagistrates;


    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $numberOfMagistrates;


    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $numberOfCourtClerks;


    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $numberOfNonJudiciaryStaff;


    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Configuration\CourtEnvironmentalStatus")
     * @ORM\JoinColumn(name="environmental_status", referencedColumnName="status_id",nullable=false)
     */
    private $environmentalStatus;



    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $courtLatitude;


    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $courtLongitude;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $courtCoordinatesDMS;


    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $lastMileConnectivityLatitude;


    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $lastMileConnectivityLongitude;


    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $lastMileConnectivityDMS;



    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $fibreDistance;



    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $areasEntitled;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $landUseDescription;


    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $economicActivitiesDescription;


    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $transportModesDescription;

    
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
    private $fourthCourtView;


    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $uniqueCourtId;


    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $courtName;


    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $courtStatus;


    /**
     * @ORM\Column(type="boolean", nullable=true,options={"default" : true})
     */
    private $courtRecordStatus;


    /**
     * @ORM\Column(type="boolean", nullable=true,options={"default" : false})
     */
    private $courtVerificationStatus;

    /**
     * @ORM\Column(type="boolean", nullable=true,options={"default" : false})
     */
    private $isPlotOnly;


    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $remarks;

    /**
     * @return mixed
     */
    public function getIsPlotOnly()
    {
        return $this->isPlotOnly;
    }

    /**
     * @param mixed $isPlotOnly
     */
    public function setIsPlotOnly($isPlotOnly)
    {
        $this->isPlotOnly = $isPlotOnly;
    }
    
    /**
     * @return mixed
     */
    public function getRemarks()
    {
        return $this->remarks;
    }

    /**
     * @param mixed $remarks
     */
    public function setRemarks($remarks)
    {
        $this->remarks = $remarks;
    }

    

    /**
     * @return mixed
     */
    public function getCourtVerificationStatus()
    {
        return $this->courtVerificationStatus;
    }

    /**
     * @param mixed $courtVerificationStatus
     */
    public function setCourtVerificationStatus($courtVerificationStatus)
    {
        $this->courtVerificationStatus = $courtVerificationStatus;
    }

    
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
    public function getMeetsFunctionality()
    {
        return $this->meetsFunctionality;
    }

    /**
     * @param mixed $meetsFunctionality
     */
    public function setMeetsFunctionality($meetsFunctionality)
    {
        $this->meetsFunctionality = $meetsFunctionality;
    }

    /**
     * @return mixed
     */
    public function getNumberOfJudges()
    {
        return $this->numberOfJudges;
    }

    /**
     * @param mixed $numberOfJudges
     */
    public function setNumberOfJudges($numberOfJudges)
    {
        $this->numberOfJudges = $numberOfJudges;
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
    public function getNumberOfNonJudiciaryStaff()
    {
        return $this->numberOfNonJudiciaryStaff;
    }

    /**
     * @param mixed $numberOfNonJudiciaryStaff
     */
    public function setNumberOfNonJudiciaryStaff($numberOfNonJudiciaryStaff)
    {
        $this->numberOfNonJudiciaryStaff = $numberOfNonJudiciaryStaff;
    }

    /**
     * @return mixed
     */
    public function getNumberOfCourtClerks()
    {
        return $this->numberOfCourtClerks;
    }

    /**
     * @param mixed $numberOfCourtClerks
     */
    public function setNumberOfCourtClerks($numberOfCourtClerks)
    {
        $this->numberOfCourtClerks = $numberOfCourtClerks;
    }

    /**
     * @return mixed
     */
    public function getCourtLatitude()
    {
        return $this->courtLatitude;
    }

    /**
     * @param mixed $courtLatitude
     */
    public function setCourtLatitude($courtLatitude)
    {
        $this->courtLatitude = $courtLatitude;
    }

    /**
     * @return mixed
     */
    public function getCourtLongitude()
    {
        return $this->courtLongitude;
    }

    /**
     * @param mixed $courtLongitude
     */
    public function setCourtLongitude($courtLongitude)
    {
        $this->courtLongitude = $courtLongitude;
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

    /**
     * @return mixed
     */
    public function getPlotNumber()
    {
        return $this->plotNumber;
    }

    /**
     * @param mixed $plotNumber
     */
    public function setPlotNumber($plotNumber)
    {
        $this->plotNumber = $plotNumber;
    }

    /**
     * @return mixed
     */
    public function getHasLastMileConnectivity()
    {
        return $this->hasLastMileConnectivity;
    }

    /**
     * @param mixed $hasLastMileConnectivity
     */
    public function setHasLastMileConnectivity($hasLastMileConnectivity)
    {
        $this->hasLastMileConnectivity = $hasLastMileConnectivity;
    }

    /**
     * @return mixed
     */
    public function getNumberOfComputers()
    {
        return $this->numberOfComputers;
    }

    /**
     * @param mixed $numberOfComputers
     */
    public function setNumberOfComputers($numberOfComputers)
    {
        $this->numberOfComputers = $numberOfComputers;
    }

    /**
     * @return mixed
     */
    public function getInternetAvailability()
    {
        return $this->internetAvailability;
    }

    /**
     * @param mixed $internetAvailability
     */
    public function setInternetAvailability($internetAvailability)
    {
        $this->internetAvailability = $internetAvailability;
    }

    /**
     * @return mixed
     */
    public function getBandwidth()
    {
        return $this->bandwidth;
    }

    /**
     * @param mixed $bandwidth
     */
    public function setBandwidth($bandwidth)
    {
        $this->bandwidth = $bandwidth;
    }

    /**
     * @return mixed
     */
    public function getAvailableSystems()
    {
        return $this->availableSystems;
    }

    /**
     * @param mixed $availableSystems
     */
    public function setAvailableSystems($availableSystems)
    {
        $this->availableSystems = $availableSystems;
    }

    /**
     * @return mixed
     */
    public function getNumberOfResidentMagistrates()
    {
        return $this->numberOfResidentMagistrates;
    }

    /**
     * @param mixed $numberOfResidentMagistrates
     */
    public function setNumberOfResidentMagistrates($numberOfResidentMagistrates)
    {
        $this->numberOfResidentMagistrates = $numberOfResidentMagistrates;
    }

    /**
     * @return mixed
     */
    public function getNumberOfDistrictMagistrates()
    {
        return $this->numberOfDistrictMagistrates;
    }

    /**
     * @param mixed $numberOfDistrictMagistrates
     */
    public function setNumberOfDistrictMagistrates($numberOfDistrictMagistrates)
    {
        $this->numberOfDistrictMagistrates = $numberOfDistrictMagistrates;
    }

    /**
     * @return mixed
     */
    public function getNumberOfMagistrates()
    {
        return $this->numberOfMagistrates;
    }

    /**
     * @param mixed $numberOfMagistrates
     */
    public function setNumberOfMagistrates($numberOfMagistrates)
    {
        $this->numberOfMagistrates = $numberOfMagistrates;
    }

    /**
     * @return mixed
     */
    public function getEnvironmentalStatus()
    {
        return $this->environmentalStatus;
    }

    /**
     * @param mixed $environmentalStatus
     */
    public function setEnvironmentalStatus($environmentalStatus)
    {
        $this->environmentalStatus = $environmentalStatus;
    }

    /**
     * @return mixed
     */
    public function getCourtCoordinatesDMS()
    {
        return $this->courtCoordinatesDMS;
    }

    /**
     * @param mixed $courtCoordinatesDMS
     */
    public function setCourtCoordinatesDMS($courtCoordinatesDMS)
    {
        $this->courtCoordinatesDMS = $courtCoordinatesDMS;
    }

    /**
     * @return mixed
     */
    public function getLastMileConnectivityLatitude()
    {
        return $this->lastMileConnectivityLatitude;
    }

    /**
     * @param mixed $lastMileConnectivityLatitude
     */
    public function setLastMileConnectivityLatitude($lastMileConnectivityLatitude)
    {
        $this->lastMileConnectivityLatitude = $lastMileConnectivityLatitude;
    }

    /**
     * @return mixed
     */
    public function getLastMileConnectivityLongitude()
    {
        return $this->lastMileConnectivityLongitude;
    }

    /**
     * @param mixed $lastMileConnectivityLongitude
     */
    public function setLastMileConnectivityLongitude($lastMileConnectivityLongitude)
    {
        $this->lastMileConnectivityLongitude = $lastMileConnectivityLongitude;
    }

    /**
     * @return mixed
     */
    public function getFibreDistance()
    {
        return $this->fibreDistance;
    }

    /**
     * @param mixed $fibreDistance
     */
    public function setFibreDistance($fibreDistance)
    {
        $this->fibreDistance = $fibreDistance;
    }

    /**
     * @return mixed
     */
    public function getAreasEntitled()
    {
        return $this->areasEntitled;
    }

    /**
     * @param mixed $areasEntitled
     */
    public function setAreasEntitled($areasEntitled)
    {
        $this->areasEntitled = $areasEntitled;
    }

    /**
     * @return mixed
     */
    public function getFourthCourtView()
    {
        return $this->fourthCourtView;
    }

    /**
     * @param mixed $fourthCourtView
     */
    public function setFourthCourtView($fourthCourtView)
    {
        $this->fourthCourtView = $fourthCourtView;
    }

    /**
     * @return mixed
     */
    public function getLastMileConnectivityDMS()
    {
        return $this->lastMileConnectivityDMS;
    }

    /**
     * @param mixed $lastMileConnectivityDMS
     */
    public function setLastMileConnectivityDMS($lastMileConnectivityDMS)
    {
        $this->lastMileConnectivityDMS = $lastMileConnectivityDMS;
    }

    /**
     * @return mixed
     */
    public function getLandUseDescription()
    {
        return $this->landUseDescription;
    }

    /**
     * @param mixed $landUseDescription
     */
    public function setLandUseDescription($landUseDescription)
    {
        $this->landUseDescription = $landUseDescription;
    }

    /**
     * @return mixed
     */
    public function getEconomicActivitiesDescription()
    {
        return $this->economicActivitiesDescription;
    }

    /**
     * @param mixed $economicActivitiesDescription
     */
    public function setEconomicActivitiesDescription($economicActivitiesDescription)
    {
        $this->economicActivitiesDescription = $economicActivitiesDescription;
    }

    /**
     * @return mixed
     */
    public function getTransportModesDescription()
    {
        return $this->transportModesDescription;
    }

    /**
     * @param mixed $transportModesDescription
     */
    public function setTransportModesDescription($transportModesDescription)
    {
        $this->transportModesDescription = $transportModesDescription;
    }

    /**
     * @return mixed
     */
    public function getCourtName()
    {
        return $this->courtName;
    }

    /**
     * @param mixed $courtName
     */
    public function setCourtName($courtName)
    {
        $this->courtName = $courtName;
    }

    /**
     * @return mixed
     */
    public function getCourtStatus()
    {
        return $this->courtStatus;
    }

    /**
     * @param mixed $courtStatus
     */
    public function setCourtStatus($courtStatus)
    {
        $this->courtStatus = $courtStatus;
    }

    /**
     * @return mixed
     */
    public function getCourtRecordStatus()
    {
        return $this->courtRecordStatus;
    }

    /**
     * @param mixed $courtRecordStatus
     */
    public function setCourtRecordStatus($courtRecordStatus)
    {
        $this->courtRecordStatus = $courtRecordStatus;
    }
    

}