<?php

namespace PouyaSoft\AppzaBundle\Form\Type;

use Symfony\Component\Form\Extension\Core\Type\CollectionType as Base;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PouyasoftCollectionType extends Base
{
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        parent::buildView($view, $form, $options);

        $view->vars['javascript'] = $options['javascript'];
        $view->vars['addButtonTitle'] = $options['addButtonTitle'];
        $view->vars['removeButtonTitle'] = $options['removeButtonTitle'];
        $view->vars['addButtonClick'] = $options['addButtonClick'];
        $view->vars['removeButtonClick'] = $options['removeButtonClick'];
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            'javascript' => true,
            'addButtonTitle' => '',
            'removeButtonTitle' => '',
            'addButtonClick' => null,
            'removeButtonClick' => null,
        ]);

        $resolver->setAllowedTypes('javascript', ['bool']);
        $resolver->setAllowedTypes('addButtonTitle', ['string']);
        $resolver->setAllowedTypes('removeButtonTitle', ['string']);
        $resolver->setAllowedTypes('addButtonClick', ['string', 'null']);
        $resolver->setAllowedTypes('removeButtonClick', ['string', 'null']);
    }

    public function getBlockPrefix() : string
    {
        return 'pouyasoft_collection';
    }
}
