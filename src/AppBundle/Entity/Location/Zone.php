<?php


namespace AppBundle\Entity\Location;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Form\Validator\Constraints as CourtMappingAssert;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\Configuration\ZoneRepository")
 * @ORM\Table(name="cfg_zones",uniqueConstraints={@ORM\UniqueConstraint(name="unique_zone_name", columns={"zone_name"})})
 */
class Zone
{

    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $zoneId;


    /**
     * @Assert\NotBlank()
     * @CourtMappingAssert\IsUniqueZone()
     * @ORM\Column(type="string", nullable=true)
     */
    private $zoneName;



    
    /**
     * @return mixed
     */
    public function getZoneName()
    {
        return $this->zoneName;
    }

    /**
     * @param mixed $zoneName
     */
    public function setZoneName($zoneName)
    {
        $this->zoneName = $zoneName;
    }

    /**
     * @return mixed
     */
    public function getZoneId()
    {
        return $this->zoneId;
    }

    /**
     * @param mixed $zoneId
     */
    public function setZoneId($zoneId)
    {
        $this->zoneId = $zoneId;
    }


}