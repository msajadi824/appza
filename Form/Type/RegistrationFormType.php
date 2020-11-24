<?php
namespace PouyaSoft\AppzaBundle\Form\Type;

use PouyaSoft\SDateBundle\Form\Type\PouyaSoftSDateType;
use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Regex;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->remove('username')
            ->add('company', TextType::class, array(
                'label' => 'نام شرکت',
                'required'=>false
            ))
            ->add('firstName', TextType::class, array(
                'label' => 'نام',
                'required' => true,
                'constraints' => [
                    new Regex(['pattern' => '/^[\x{0600}-\x{06FF}\x{FB8A}\x{067E}\x{0686}\x{06AF}\s]+$/u'])
                ]
            ))
            ->add('lastName', TextType::class, array(
                'label' => 'نام خانوادگی',
                'required' => true,
                'constraints' => [
                    new Regex(['pattern' => '/^[\x{0600}-\x{06FF}\x{FB8A}\x{067E}\x{0686}\x{06AF}\s]+$/u'])
                ]
            ))
            ->add('codeMelli', TextType::class, array(
                'label' => 'کد ملی',
                'required' => true,
                'attr' => ['class' => 'integer_validation', 'maxlength' => '10'],
                'constraints' => [
                    new Regex(['pattern' => '/^[0-9]{10,11}$/'])
                ]
            ))
            ->add('birthDate', PouyaSoftSDateType::class, array(
                'label' => 'تاریخ تولد',
                'required' => true,
                'serverFormat' => 'yyyy/MM/dd',
                'pickerOptions' => [
                    'Format' => 'yyyy/MM/dd',
                    'EnableTimePicker' => false,
                ],
            ))
            ->add('mobile', TextType::class, array(
                'label' => 'موبایل',
                'required' => true,
                'attr' => ['class' => 'integer_validation', 'maxlength' => '11'],
                'constraints' => [
                    new Regex(['pattern' => '/^09[0-9]{9}$/'])
                ]
            ))
            ->add('phone', TextType::class, array(
                'label' => 'تلفن',
                'required' => true,
                'attr' => ['class' => 'integer_validation', 'maxlength' => '11'],
                'constraints' => [
                    new Regex(['pattern' => '/^0[^09][0-9]{9}$/'])
                ]
            ))
            ->add('address', TextType::class, array(
                'label' => 'آدرس',
                'required' => true
            ))
            ->add('policy', CheckboxType::class, [
                'required' => true,
                'mapped' => false,
                'label' => "قوانین را قبول دارم"
            ])
            ->addEventListener(FormEvents::SUBMIT,function(FormEvent $event){
                $data = $event->getData();
                if (!$data instanceof User) {
                    return;
                }
                // just transfer the email field to the username field
                $data->setUsername($data->getEmail());
            })
        ;
    }

    public function getParent()
    {
        return 'FOS\UserBundle\Form\Type\RegistrationFormType';
    }

    public function getBlockPrefix()
    {
        return 'pouyasoft_bundle_registration';
    }
}