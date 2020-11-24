<?php

namespace PouyaSoft\AppzaBundle\Form;

use App\Entity\User;
use App\Form\Type\PouyasoftFileType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', TextType::class, array(
                'label' => 'نام کاربری',
                'required' => true,
            ))
            ->add('fullName', null, [
                'label' => 'نام و نام خانوادگی',
                'required' => false,
                'attr' => ['refresh' => true],
            ])
            ->add('picFile', PouyasoftFileType::class, [
                'label' => "تصویر حساب کاربری",
                'allow_delete' => true,
                'required' => false,
            ])
            ->add('mobile', TextType::class, [
                'label' => 'شماره همراه',
                'required' => false
            ])
            ->addEventListener(FormEvents::SUBMIT, function(FormEvent $event) {
                $data = $event->getData();
                if (!$data instanceof User) return;

                $data->setEmail($data->getUsername() . '@test.test');
            })
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'App\Entity\User'
        ));
    }

    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'pouyasoft_bundle_user';
    }
}
