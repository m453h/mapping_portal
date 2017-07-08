<?php


namespace AppBundle\Entity\Configuration;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="cfg_court_levels")
 */
class CourtLevel
{

    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $levelId;


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
    public function getLevelId()
    {
        return $this->levelId;
    }

    /**
     * @param mixed $levelId
     */
    public function setLevelId($levelId)
    {
        $this->levelId = $levelId;
    }


}