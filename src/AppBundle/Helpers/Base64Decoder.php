<?php


namespace AppBundle\Helpers;

class Base64Decoder
{

    private $uploadPath;

    private $isTagged;
    
    private $imageTag;
    
    private $fileType;
    
    
    public function  __construct()
    {
        $this->isTagged = false;
        $this->fileType = 'jpg';
    }
    
    public function decodeBase64($content)
    {
        $fileUploaded=null;

        //Determine the path to which we want to save this file
        $cleanName=time().$this->generateRandomString(11);

        $myFileName="$cleanName.$this->fileType";

        $newName = $this->uploadPath.'/'.$myFileName;

        //Check if the file with the same name is already exists on the server
        if (!file_exists($newName))
        {
            //Attempt to move the uploaded file to it's new place
            if($this->isTagged == true)
            {
                $this->tagImage(base64_decode($content),$this->imageTag,$newName);
            }
            else
            {
                file_put_contents($newName,base64_decode($content));
            }

            $fileUploaded = $myFileName;
        }

        return $fileUploaded;

    }

    public function tagImage($data,$text,$path)
    {
        // Create Image From Existing File
        $img = imagecreatefromstring($data);

        $imgHeight = imagesy($img);

        $yPosition = $imgHeight-15;

        $xPosition = 10;

        // Allocate A Color For The Text
        $white = imagecolorallocate($img, 255, 255, 0);

        // Set Path to Font File
        $fontPath = '../public/assets/fonts/digital.ttf';

        // Print Text On Image
        imagettftext($img, 20, 0, $xPosition, $yPosition, $white, $fontPath, $text);

        // Send Image to Path
        imagejpeg($img,$path);

        // Clear Memory
        imagedestroy($img);
    }

    public function generateRandomString($length)
    {
        $characters = 'abcdefghijklmnopqrstuvwxyz0123456789';

        $string = '';

        for ($i = 0; $i < $length; $i++)
        {
            $string .= $characters[rand(0, strlen($characters) - 1)];
        }

        return $string;
    }

    /**
     * @return mixed
     */
    public function getUploadPath()
    {
        return $this->uploadPath;
    }

    /**
     * @param mixed $uploadPath
     */
    public function setUploadPath($uploadPath)
    {
        $this->uploadPath = $uploadPath;
    }

    /**
     * @return mixed
     */
    public function getIsTagged()
    {
        return $this->isTagged;
    }

    /**
     * @param mixed $isTagged
     */
    public function setIsTagged($isTagged)
    {
        $this->isTagged = $isTagged;
    }

    /**
     * @return mixed
     */
    public function getImageTag()
    {
        return $this->imageTag;
    }

    /**
     * @param mixed $imageTag
     */
    public function setImageTag($imageTag)
    {
        $this->imageTag = $imageTag;
    }

    /**
     * @return mixed
     */
    public function getFileType()
    {
        return $this->fileType;
    }

    /**
     * @param mixed $fileType
     */
    public function setFileType($fileType)
    {
        $this->fileType = $fileType;
    }
    
}