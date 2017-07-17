<?php


namespace AppBundle\Entity\Court;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\Court\CourtRepository")
 * @ORM\Table(name="tbl_court_economic_activities")
 */
class CourtEconomicActivities
{

    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer",options={"default"="nextval('tbl_court_transport_modes_record_no_seq')"})
     */
    private $recordNo;


    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Court\Court")
     * @ORM\JoinColumn(name="court_id", referencedColumnName="court_id",nullable=false)
     */
    private $court;
    

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Configuration\EconomicActivity")
     * @ORM\JoinColumn(name="activity_id", referencedColumnName="activity_id",nullable=false)
     */
    private $economicActivity;

    
    /**
     * @return mixed
     */
    public function getEconomicActivity()
    {
        return $this->economicActivity;
    }

    /**
     * @param mixed $economicActivity
     */
    public function setEconomicActivity($economicActivity)
    {
        $this->economicActivity = $economicActivity;
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