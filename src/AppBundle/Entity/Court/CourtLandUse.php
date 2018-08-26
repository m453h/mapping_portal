<?php


namespace AppBundle\Entity\Court;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\Court\CourtRepository")
 * @ORM\Table(name="tbl_court_land_uses")
 */
class CourtLandUse
{

    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $recordNo;


    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Court\Court")
     * @ORM\JoinColumn(name="court_id", referencedColumnName="court_id",nullable=false,onDelete="CASCADE")
     */
    private $court;
    

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Configuration\LandUse")
     * @ORM\JoinColumn(name="activity_id", referencedColumnName="activity_id",nullable=false)
     */
    private $landUse;

    
    /**
     * @return mixed
     */
    public function getLandUse()
    {
        return $this->landUse;
    }

    /**
     * @param mixed $landUse
     */
    public function setLandUse($landUse)
    {
        $this->landUse = $landUse;
    }

    /**
     * @return mixed
     */
    public function getRecordNo()
    {
        return $this->recordNo;
    }

    /**
     * @param mixed $recordNo
     */
    public function setRecordNo($recordNo)
    {
        $this->recordNo = $recordNo;
    }

    /**
     * @return mixed
     */
    public function getCourt()
    {
        return $this->court;
    }

    /**
     * @param mixed $court
     */
    public function setCourt($court)
    {
        $this->court = $court;
    }

    
}