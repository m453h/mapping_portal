<?php


namespace AppBundle\Entity\Configuration;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\Configuration\CourtEnvironmentalStatusRepository")
 * @ORM\Table(name="cfg_court_environmental_status")
 */
class CourtEnvironmentalStatus
{

    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $statusId;


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
    public function getStatusId()
    {
        return $this->statusId;
    }

    /**
     * @param mixed $statusId
     */
    public function setStatusId($statusId)
    {
        $this->statusId = $statusId;
    }


}