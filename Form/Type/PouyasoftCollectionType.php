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
        $view->vars['javascript_add_title'] = $options['javascript_add_title'];
        $view->vars['javascript_add_click_function_name'] = $options['javascript_add_click_function_name'];
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            'javascript' => true,
            'javascript_add_title' => 'مورد جدید',
            'javascript_add_click_function_name' => null,
        ]);

        $resolver->setAllowedTypes('javascript', ['bool']);
        $resolver->setAllowedTypes('javascript_add_title', ['string', 'null']);
        $resolver->setAllowedTypes('javascript_add_click_function_name', ['string', 'null']);
    }

    public function getBlockPrefix() : string
    {
        return 'pouyasoft_collection';
    }
}
