<?php

namespace AppBundle\Form\Validator\Constraints;

use AppBundle\Entity\Location\Region;
use AppBundle\Entity\Location\Zone;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class IsUniqueVillageStreetValidator extends ConstraintValidator
{

    /**
     * @var EntityManager
     */
    private $em;

    public function  __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function validate($value, Constraint $constraint)
    {
        $data = $this->context->getRoot()->getData();

        $areaName = $data->getAreaName();

        if($areaName!=null)
        {
            $id = $this->em->getRepository('AppBundle:Location\VillageStreet')
                ->findVillageStreetByName($areaName);

            if ($id!=$data->getAreaId()) {
                $this->context->buildViolation($constraint->message)
                    ->setParameter('{{ string }}', 'This village/street already exists')
                    ->addViolation();
            }
        }

    }
}