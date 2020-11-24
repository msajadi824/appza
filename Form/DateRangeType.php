<?php

namespace PouyaSoft\AppzaBundle\Form;

use PouyaSoft\SDateBundle\Form\Type\PouyaSoftSDateType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DateRangeType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('dateFrom', PouyaSoftSDateType::class, [
                'label' => "تاریخ از",
                'required' => true,
                'pickerOptions' => [
                    'groupId' => 'date_range',
                    'fromDate' => true,
                    'toDate' => false,
                ],
            ])
            ->add('dateTo', PouyaSoftSDateType::class, [
                'label' => "تاریخ تا",
                'required' => true,
                'pickerOptions' => [
                    'groupId' => 'date_range',
                    'fromDate' => false,
                    'toDate' => true,
                ],
            ])
        ;
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
//            'attr' => ['target' => '_blank']
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'pouyasoft_bundle_date_range';
    }


}
