<?php

namespace AppBundle\Form\Validator\Constraints;

use AppBundle\Entity\Location\Region;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class IsUniqueZoneValidator extends ConstraintValidator
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

        $zoneName = $data->getZoneName();

        if($zoneName!=null)
        {
            $id = $this->em->getRepository('AppBundle:Location\Zone')
                ->findZoneIdByName($zoneName);

            if ($id!=$data->getZoneId()) {
                $this->context->buildViolation($constraint->message)
                    ->setParameter('{{ string }}', 'This zone already exists')
                    ->addViolation();
            }
        }

    }
}