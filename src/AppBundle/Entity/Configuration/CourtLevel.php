<?php


namespace AppBundle\Entity\Configuration;

use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\Configuration\CourtLevelRepository")
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
     * @Assert\NotBlank()
     * @ORM\Column(type="string", nullable=true)
     */
    private $description;


    /**
     * @Assert\NotBlank()
     * @ORM\Column(type="string", nullable=true)
     */
    private $descriptionSw;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $identifier;


    /**
     * @Assert\NotBlank()
     * @ORM\Column(type="integer", nullable=true)
     */
    private $hierarchy;


    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $details;

    
    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param $locale
     * @return mixed
     */
    public function getLocalizedDescription($locale)
    {
        dump($locale);
        return $this->description;
    }


    /**
     * @param mixed $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
        $description = preg_replace('@[^A-Za-z0-9\w\ ]@', "", $description);
        $this->identifier = strtolower(str_replace(' ','-',$description));
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

    /**
     * @return mixed
     */
    public function getHierarchy()
    {
        return $this->hierarchy;
    }

    /**
     * @param mixed $hierarchy
     */
    public function setHierarchy($hierarchy)
    {
        $this->hierarchy = $hierarchy;
    }

    /**
     * @return mixed
     */
    public function getDetails()
    {
        return $this->details;
    }

    /**
     * @param mixed $details
     */
    public function setDetails($details)
    {
        $this->details = $details;
    }

    /**
     * @return mixed
     */
    public function getDescriptionSw()
    {
        return $this->descriptionSw;
    }

    /**
     * @param mixed $descriptionSw
     */
    public function setDescriptionSw($descriptionSw)
    {
        $this->descriptionSw = $descriptionSw;
    }

    /**
     * @return mixed
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }

    /**
     * @param mixed $identifier
     */
    public function setIdentifier($identifier)
    {
        $this->identifier = $identifier;
    }

}