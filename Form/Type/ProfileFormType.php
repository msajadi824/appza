<?php
namespace PouyaSoft\AppzaBundle\Form\Type;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Regex;

class ProfileFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->remove('email')
            ->add('fullName', TextType::class, array(
                'label' => 'نام و نام خانوادگی',
                'required' => true,
                'constraints' => [
                    new Regex(['pattern' => '/^[\x{0600}-\x{06FF}\x{FB8A}\x{067E}\x{0686}\x{06AF}\s]+$/u'])
                ]
            ))
            ->add('mobile', TextType::class, array(
                'label' => 'موبایل',
                'required' => false,
                'attr' => ['class' => 'integer_validation', 'maxlength' => '11'],
                'constraints' => [
                    new Regex(['pattern' => '/^09[0-9]{9}$/'])
                ]
            ))
            ->add('picFile', PouyasoftFileType::class, [
                'label' => "تصویر حساب کاربری",
                'allow_delete' => true,
                'required' => false,
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'profile.edit.submit',
                'translation_domain' => 'FOSUserBundle',
            ])
            ->addEventListener(FormEvents::SUBMIT, function(FormEvent $event) {
                $data = $event->getData();
                if (!$data instanceof User) return;

                $data->setEmail($data->getUsername() . '@test.test');
            })
        ;
    }

    public function getParent()
    {
        return 'FOS\UserBundle\Form\Type\ProfileFormType';
    }

    public function getBlockPrefix()
    {
        return 'pouyasoft_bundle_profile';
    }
}