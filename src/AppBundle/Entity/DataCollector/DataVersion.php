<?php


namespace AppBundle\Entity\DataCollector;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\AppUsers\UserRepository")
 * @ORM\Table(name="app_data_versions")
 */
class DataVersion
{

    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $versionId;


    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updateTime;

    
    /**
     * @return mixed
     */
    public function getUpdateTime()
    {
        return $this->updateTime;
    }

    /**
     * @param mixed $updateTime
     */
    public function setUpdateTime($updateTime)
    {
        $this->updateTime = $updateTime;
    }

    /**
     * @return mixed
     */
    public function getVersionId()
    {
        return $this->versionId;
    }

    /**
     * @param mixed $versionId
     */
    public function setVersionId($versionId)
    {
        $this->versionId = $versionId;
    }

    

}