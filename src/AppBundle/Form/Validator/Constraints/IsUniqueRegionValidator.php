<?php

namespace AppBundle\Form\Validator\Constraints;

use AppBundle\Entity\Location\Region;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class IsUniqueRegionValidator extends ConstraintValidator
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

        $regionName = $data->getRegionName();

        if($regionName!=null)
        {
            $total = $this->em->getRepository('AppBundle:Location\Region')
                ->findTotalByName($regionName);

            if ($total > 0) {
                $this->context->buildViolation($constraint->message)
                    ->setParameter('{{ string }}', 'This region already exists')
                    ->addViolation();
            }
        }

    }
}