<?php


namespace AppBundle\Entity\Configuration;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\Configuration\TransportModeRepository")
 * @ORM\Table(name="cfg_transport_modes")
 */
class TransportMode
{

    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $modeId;


    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $description;



    
    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param mixed $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return mixed
     */
    public function getModeId()
    {
        return $this->modeId;
    }

    /**
     * @param mixed $modeId
     */
    public function setModeId($modeId)
    {
        $this->modeId = $modeId;
    }


}