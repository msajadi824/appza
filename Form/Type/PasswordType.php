<?php

namespace PouyaSoft\AppzaBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;

class PasswordType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('plainPassword', RepeatedType::class, array(
                'type' => \Symfony\Component\Form\Extension\Core\Type\PasswordType::class,
                'first_options' => array('label' => 'کلمه عبور جدید'),
                'second_options' => array('label' => 'تائید کلمه عبور جدید')
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'pouyasoft_bundle_password';
    }


}
