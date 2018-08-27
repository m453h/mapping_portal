<?php

namespace AppBundle\Form\Validator\Constraints;

use AppBundle\Entity\Location\Region;
use AppBundle\Entity\Location\Zone;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class IsUniqueDistrictValidator extends ConstraintValidator
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

        $districtName = $data->getDistrictName();

        if($districtName!=null)
        {
            $districtId = $this->em->getRepository('AppBundle:Location\District')
                ->findDistrictIdByName($districtName);

            if ($districtId!=$data->getDistrictId()) {
                $this->context->buildViolation($constraint->message)
                    ->setParameter('{{ string }}', 'This district already exists')
                    ->addViolation();
            }
        }

    }
}