<?php

namespace AppBundle\Form\Validator\Constraints;

use AppBundle\Entity\Location\Region;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class IsUniqueWardValidator extends ConstraintValidator
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

        $wardName = $data->getWardName();

        if($wardName!=null)
        {
            if($wardName!=null)
            {
                $id = $this->em->getRepository('AppBundle:Location\Ward')
                    ->findWardIdByName($wardName);

                if ($id!=$data->getWardId()) {
                    $this->context->buildViolation($constraint->message)
                        ->setParameter('{{ string }}', 'This ward already exists')
                        ->addViolation();
                }
            }
        }

    }
}