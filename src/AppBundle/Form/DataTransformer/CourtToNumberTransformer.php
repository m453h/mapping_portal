<?php

namespace AppBundle\Form\DataTransformer;

use AppBundle\Entity\Court\Court;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class CourtToNumberTransformer implements DataTransformerInterface
{
    private $manager;

    public function __construct(ObjectManager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * Transforms an object (Court) to a string (number).
     *
     * @param  Court|null $court
     * @return string
     */
    public function transform($court)
    {
        if ( !$court instanceof Court) {
            return '';
        }

        return [$court->getCourtId()];
    }

    /**
     * Transforms a string (number) to an object (District).
     *
     * @param  string $courtId
     * @return Court|null
     * @throws TransformationFailedException if object (issue) is not found.
     */
    public function reverseTransform($courtId)
    {
        // no issue number? It's optional, so that's ok
        if (!$courtId) {
            return;
        }

        $court = $this->manager
            ->getRepository('AppBundle:Court\Court')
            // query for the issue with this id
            ->find($courtId);

        if (null === $court) {
            // causes a validation error
            // this message is not shown to the user
            // see the invalid_message option
            throw new TransformationFailedException(sprintf(
                'A court with ID "%s" does not exist!',
                $courtId
            ));
        }

        return $court;
    }
}