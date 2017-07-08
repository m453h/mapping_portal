<?php

namespace AppBundle\Form\DataTransformer;


use AppBundle\Entity\Configuration\Curriculum;
use AppBundle\Entity\Location\District;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class DistrictToNumberTransformer implements DataTransformerInterface
{
    private $manager;

    public function __construct(ObjectManager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * Transforms an object (District) to a string (number).
     *
     * @param  District|null $district
     * @return string
     */
    public function transform($district)
    {
        if (null === $district) {
            return '';
        }

        return [$district->getDistrictId()];
    }

    /**
     * Transforms a string (number) to an object (District).
     *
     * @param  string $districtId
     * @return District|null
     * @throws TransformationFailedException if object (issue) is not found.
     */
    public function reverseTransform($districtId)
    {
        // no issue number? It's optional, so that's ok
        if (!$districtId) {
            return;
        }

        $district = $this->manager
            ->getRepository('AppBundle:Location\District')
            // query for the issue with this id
            ->find($districtId);

        if (null === $district) {
            // causes a validation error
            // this message is not shown to the user
            // see the invalid_message option
            throw new TransformationFailedException(sprintf(
                'A district with ID "%s" does not exist!',
                $districtId
            ));
        }

        return $district;
    }
}