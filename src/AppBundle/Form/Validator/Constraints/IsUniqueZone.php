<?php


namespace AppBundle\Form\Validator\Constraints;


use Symfony\Component\Validator\Constraint;


/**
 * @Annotation
 */
class IsUniqueZone extends Constraint
{
    public $message = '{{ string }}';

    public function validatedBy()
    {
        return get_class($this).'Validator';
    }
}