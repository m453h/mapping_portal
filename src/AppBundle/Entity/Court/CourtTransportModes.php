<?php


namespace AppBundle\Entity\Court;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\Court\CourtRepository")
 * @ORM\Table(name="tbl_court_transport_modes")
 */
class CourtTransportModes
{

    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $recordNo;


    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Court\Court")
     * @ORM\JoinColumn(name="court_id", referencedColumnName="court_id",nullable=false)
     */
    private $court;
    

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Configuration\TransportMode")
     * @ORM\JoinColumn(name="mode_id", referencedColumnName="mode_id",nullable=false)
     */
    private $transportMode;

    
    /**
     * @return mixed
     */
    public function getTransportMode()
    {
        return $this->transportMode;
    }

    /**
     * @param mixed $transportMode
     */
    public function setTransportMode($transportMode)
    {
        $this->transportMode = $transportMode;
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