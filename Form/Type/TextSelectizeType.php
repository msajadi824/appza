<?php

namespace PouyaSoft\AppzaBundle\Form\Type;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TextSelectizeType extends AbstractType
{
    /** @var  EntityManagerInterface */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $items = $options['items'];
        $items = is_callable($items) ? $items($this->em) : $items;
        $view->vars['items'] = array_map(function ($item) {return ['item' => $item];}, $items);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'items' => [],
        ]);

        $resolver->setAllowedTypes('items', ['array', 'callable']);
    }

    public function getParent()
    {
        return TextType::class;
    }

    public function getBlockPrefix()
    {
        return 'text_selectize';
    }
}
