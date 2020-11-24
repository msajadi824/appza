<?php

namespace PouyaSoft\AppzaBundle\Form\Type;

use CrEOF\Spatial\PHP\Types\Geometry\Point;
use Exception;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Regex;

class PositionType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $required = $options['required'];
        $hiddenFields = $options['hidden_fields'];

        $builder
            ->add('longitude', $hiddenFields ? HiddenType::class : TextType::class, array(
                'label' => 'طول جغرافیایی',
                'required' => $required,
                'constraints' => [
                    new Regex(['pattern' => '/^[-+]?(180(\.0+)?|((1[0-7]\d)|([1-9]?\d))(\.\d+)?)$/u'])
                ],
                'mapped' => false
            ))
            ->add('latitude', $hiddenFields ? HiddenType::class : TextType::class, array(
                'label' => 'عرض جغرافیایی',
                'required' => $required,
                'constraints' => [
                    new Regex(['pattern' => '/^[-+]?([1-8]?\d(\.\d+)?|90(\.0+)?)$/u'])
                ],
                'mapped' => false
            ))
            ->addEventListener(FormEvents::POST_SET_DATA, function(FormEvent $event) {
                /** @var Point $data */
                $data = $event->getData();
                $form = $event->getForm();

                if($data) {
                    $form->get('longitude')->setData($data->getLongitude());
                    $form->get('latitude')->setData($data->getLatitude());
                }
            })
            ->addEventListener(FormEvents::SUBMIT, function(FormEvent $event) {
                $formLongitude = $event->getForm()->get('longitude');
                $formLatitude = $event->getForm()->get('latitude');

                if($formLatitude->getData() && $formLongitude->getData()) {
                    try { $event->setData(new Point($formLongitude->getData(), $formLatitude->getData())); }
                    catch(Exception $e) {}
                }
                elseif(!$formLatitude->getData() && !$formLongitude->getData())
                    $event->setData(null);
                elseif(!$formLongitude->getData())
                    $formLongitude->addError(new FormError('لطفا طول جغرافیایی را وارد نمایید.'));
                elseif(!$formLatitude->getData())
                    $formLatitude->addError(new FormError('لطفا عرض جغرافیایی را وارد نمایید.'));
            })
        ;
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['map'] = $options['map'];
        $view->vars['map_attr'] = $options['map_attr'];
        $view->vars['map_src'] = $options['map_src'];
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'map' => true,
            'hidden_fields' => true,
            'map_attr' => [],
            'map_src' => '',
        ]);

        $resolver->setAllowedTypes('map', ['bool']);
        $resolver->setAllowedTypes('hidden_fields', ['bool']);
        $resolver->setAllowedTypes('map_attr', ['array']);
        $resolver->setAllowedTypes('map_src', ['string']);
    }

    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'position';
    }
}
