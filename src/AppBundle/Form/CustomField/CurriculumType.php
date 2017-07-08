<?php

namespace AppBundle\Form\CustomField;


use AppBundle\Form\DataTransformer\CurriculumToNumberTransformer;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CurriculumType extends AbstractType
{

    private $em;
    

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'placeholder' => 'Select curriculum',
            'choices' => [],
        ));
    }
    
    /**
     * StudentCustomType constructor.
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->em = $entityManager;
    }

    public function buildForm(FormBuilderInterface $builder, array $options) {

        $builder->addModelTransformer(new CurriculumToNumberTransformer($this->em));
    }


    public function getParent()
    {
        return ChoiceType::class;
    }




    
}