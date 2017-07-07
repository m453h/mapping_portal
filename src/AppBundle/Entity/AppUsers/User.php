<?php


namespace AppBundle\Entity\AppUsers;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\UniqueConstraint;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\AppUsers\UserRepository")
 * @ORM\Table(name="app_users")
 * @ORM\Table(name="app_users",uniqueConstraints={@UniqueConstraint(name="unique_app_users", columns={"mobile"})})
 */
class User
{

    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $userId;


    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $firstName;


    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $surname;


    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $mobile;
    
    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $username;



    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $password;


    /**
     * @ORM\Column(type="integer",nullable=true)
     */
    private $loginTries;


    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $dateCreated;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $lastActivity;


    /**
     * @ORM\Column(type="integer",nullable=true,options={"default" : 6})
     */
    private $passwordValidity;


    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $lastPasswordUpdateDate;


    /**
     * @ORM\Column(type="string", length=1, options={"fixed" = true})
     */
    private $accountStatus;
    
    
    
    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $token;




    /**
     * @return mixed
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * @param mixed $userId
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;
    }

    /**
     * @return mixed
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * @param mixed $firstName
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;
    }

    /**
     * @return mixed
     */
    public function getSurname()
    {
        return $this->surname;
    }

    /**
     * @return mixed
     */
    public function getFullName()
    {
        return $this->firstName.' '.$this->surname;
    }



    /**
     * @param mixed $surname
     */
    public function setSurname($surname)
    {
        $this->surname = $surname;
    }

    /**
     * @return mixed
     */
    public function getMobile()
    {
        return $this->mobile;
    }

    /**
     * @param mixed $mobile
     */
    public function setMobile($mobile)
    {
        $this->mobile = $mobile;
    }
    
    /**
     * @return mixed
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @param mixed $username
     */
    public function setUsername($username)
    {
        $this->username = $username;
    }

    /**
     * @return mixed
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param mixed $password
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }
    
    /**
     * @return mixed
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * @param mixed $token
     */
    public function setToken($token)
    {
        $this->token = $token;
    }

    /**
     * @return mixed
     */
    public function getLoginTries()
    {
        return $this->loginTries;
    }

    /**
     * @param mixed $loginTries
     */
    public function setLoginTries($loginTries)
    {
        $this->loginTries = $loginTries;
    }

    /**
     * @return mixed
     */
    public function getDateCreated()
    {
        return $this->dateCreated;
    }

    /**
     * @param mixed $dateCreated
     */
    public function setDateCreated($dateCreated)
    {
        $this->dateCreated = $dateCreated;
    }

    /**
     * @return mixed
     */
    public function getLastActivity()
    {
        return $this->lastActivity;
    }

    /**
     * @param mixed $lastActivity
     */
    public function setLastActivity($lastActivity)
    {
        $this->lastActivity = $lastActivity;
    }

    /**
     * @return mixed
     */
    public function getPasswordValidity()
    {
        return $this->passwordValidity;
    }

    /**
     * @param mixed $passwordValidity
     */
    public function setPasswordValidity($passwordValidity)
    {
        $this->passwordValidity = $passwordValidity;
    }

    /**
     * @return mixed
     */
    public function getLastPasswordUpdateDate()
    {
        return $this->lastPasswordUpdateDate;
    }

    /**
     * @param mixed $lastPasswordUpdateDate
     */
    public function setLastPasswordUpdateDate($lastPasswordUpdateDate)
    {
        $this->lastPasswordUpdateDate = $lastPasswordUpdateDate;
    }

    /**
     * @return mixed
     */
    public function getAccountStatus()
    {
        return $this->accountStatus;
    }

    /**
     * @param mixed $accountStatus
     */
    public function setAccountStatus($accountStatus)
    {
        $this->accountStatus = $accountStatus;
    }
    
}