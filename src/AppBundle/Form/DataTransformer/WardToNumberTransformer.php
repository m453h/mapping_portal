<?php

namespace AppBundle\Form\DataTransformer;


use AppBundle\Entity\Configuration\Curriculum;
use AppBundle\Entity\Location\District;
use AppBundle\Entity\Location\Ward;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class WardToNumberTransformer implements DataTransformerInterface
{
    private $manager;

    public function __construct(ObjectManager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * Transforms an object (Ward) to a string (number).
     *
     * @param  Ward|null $ward
     * @return string
     */
    public function transform($ward)
    {
        if (null === $ward) {
            return '';
        }

        return [$ward->getWardId()];
    }

    /**
     * Transforms a string (number) to an object (District).
     *
     * @param  string $wardId
     * @return Ward|null
     * @throws TransformationFailedException if object (issue) is not found.
     */
    public function reverseTransform($wardId)
    {
        // no issue number? It's optional, so that's ok
        if (!$wardId) {
            return;
        }

        $ward = $this->manager
            ->getRepository('AppBundle:Location\Ward')
            // query for the issue with this id
            ->find($wardId);

        if (null === $ward) {
            // causes a validation error
            // this message is not shown to the user
            // see the invalid_message option
            throw new TransformationFailedException(sprintf(
                'A ward with ID "%s" does not exist!',
                $wardId
            ));
        }

        return $ward;
    }
}