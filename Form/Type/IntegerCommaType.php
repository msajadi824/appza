<?php

namespace PouyaSoft\AppzaBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;

class IntegerCommaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addViewTransformer(new IntegerCommaTransformer());
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        if(!isset($view->vars['attr']['onkeyup']))
            $view->vars['attr']['onkeyup'] = "this.value = (this.value ? parseFloat($(this).val().replace(/,/g, '')) : 0).toString().replace(/\B(?=(\d{3})+(?!\d))/g, \",\");";
    }

    public function getParent()
    {
        return TextType::class;
    }
}