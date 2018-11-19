<?php


namespace AppBundle\Entity\Configuration;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\Configuration\ContactRepository")
 * @ORM\Table(name="tbl_web_about")
 */
class About
{

    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $aboutId;


    /**
     * @Assert\NotBlank()
     * @ORM\Column(type="text", nullable=true)
     */
    private $textEn;

    /**
     * @Assert\NotBlank()
     * @ORM\Column(type="text", nullable=true)
     */
    private $textSw;

    /**
     * @return mixed
     */
    public function getAboutId()
    {
        return $this->aboutId;
    }

    /**
     * @param mixed $aboutId
     */
    public function setAboutId($aboutId)
    {
        $this->aboutId = $aboutId;
    }

    /**
     * @return mixed
     */
    public function getTextEn()
    {
        return $this->textEn;
    }

    /**
     * @param mixed $textEn
     */
    public function setTextEn($textEn)
    {
        $this->textEn = $textEn;
    }

    /**
     * @return mixed
     */
    public function getTextSw()
    {
        return $this->textSw;
    }

    /**
     * @param mixed $textSw
     */
    public function setTextSw($textSw)
    {
        $this->textSw = $textSw;
    }

    /**
     * @param $locale
     * @return mixed
     */
    public function getLocalizedDetails($locale)
    {
        if($locale=='sw')
            return $this->textSw;
        return $this->textEn;
    }
}