<?php

namespace AppBundle\Form\Reports;

use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CustomReportBuilderFormType extends  AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('courtLevel', EntityType::class, [
                'placeholder' => 'Choose court level',
                'choice_label' => 'description',
                'mapped'=>false,
                'required'=>false,
                'class' => 'AppBundle\Entity\Configuration\CourtLevel',
                'query_builder' => function(EntityRepository  $er) {
                    return $er->createQueryBuilder('r')
                        ->orderBy('r.description', 'ASC');
                }
            ])
            ->add('region', EntityType::class, [
                'placeholder' => 'Choose a region',
                'choice_label' => 'regionName',
                'mapped'=>false,
                'required'=>false,
                'class' => 'AppBundle\Entity\Location\Region',
                'query_builder' => function(EntityRepository  $er) {
                    return $er->createQueryBuilder('r')
                        ->orderBy('r.regionName', 'ASC');
                }
            ])
            ->add('district', ChoiceType::class, array(
                'placeholder' => 'Choose District',
                'choices' => [],
                'mapped' => false,
                'required' => false
            ))
            ->add('ward', ChoiceType::class, array(
                'placeholder' => 'Choose Ward',
                'choices' => [],
                'mapped' => false,
                'required' => false
            ))
            ->add('columns', ChoiceType::class, array(
                'placeholder' => 'Choose Columns To Include',
                'label'=>'Columns To Include',
                'multiple' =>true,
                'expanded'=>false,
                'attr'=>['class'=>'multi-select'],
                'choices' => [
                    'Court Name'=>'court_name',
                    'Court Code'=>'court_code',
                    'Court Level'=>'court_level',
                    'Land Ownership Status'=>'land_ownership_status',
                    'Court Building Status'=>'court_building_status',
                    'Environmental Status'=>'environmental_status',
                    'Year Constructed'=>'year_constructed',
                    'Land Survey Status'=>'is_land_surveyed',
                    'Title Deed'=>'has_title_deed',
                    'Plot Number'=>'plot_number',
                    'Meets Functionality ?'=>'meets_functionality',
                    'Last Mile Connectivity'=>'has_last_mile_connectivity',
                    'Number of Computers'=>'number_of_computers',
                    'Internet Availability'=>'internet_availability',
                    'Bandwidth'=>'bandwidth',
                    'Available Systems'=>'available_systems',
                    'Cases Per Year'=>'cases_per_year',
                    'Population Served'=>'population_served',
                    'Number of Justices'=>'number_of_justices',
                    'Number of Judges'=>'number_of_judges',
                    'Number of Resident Magistrates'=>'number_of_resident_magistrates',
                    'Number_of District Magistrates'=>'number_of_district_magistrates',
                    'Number of Magistrates'=>'number_of_magistrates',
                    'Number of Court Clerks'=>'number_of_court_clerks',
                    'Number of Non Judiciary Staff'=>'number_of_non_judiciary_staff',
                    'Court Latitude'=>'court_latitude',
                    'Court Longitude'=>'court_longitude',
                    'Areas Entitled'=>'areas_entitled',
                ],
                'mapped' => false,
                'required' => false
            ))
            ->add('preview', SubmitType::class, array(
                'label' => 'Preview Report',
                'attr' =>['class'=>'btn-blue btn-preview']
            ))
            ->add('pdf', SubmitType::class, array(
                'label' => 'Export to PDF',
                'attr'=> ['class'=>'btn-red btn-pdf']
            ));

        $builder->get('region')->resetViewTransformers();
        $builder->get('district')->resetViewTransformers();
        $builder->get('ward')->resetViewTransformers();

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
        ]);
    }


}