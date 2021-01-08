<?php

namespace PouyaSoft\AppzaBundle\Form\Type;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\Extension\Core\Type\CollectionType as Base;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PouyasoftCollectionType extends Base
{
    /** @var  EntityManagerInterface */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

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
            'changeInDatabase' => false,
        ]);

        $resolver->setAllowedTypes('javascript', ['bool']);
        $resolver->setAllowedTypes('addButtonTitle', ['string']);
        $resolver->setAllowedTypes('removeButtonTitle', ['string']);
        $resolver->setAllowedTypes('addButtonClick', ['string', 'null']);
        $resolver->setAllowedTypes('removeButtonClick', ['string', 'null']);
        $resolver->setAllowedTypes('changeInDatabase', ['bool']);
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        if($options['changeInDatabase']) {

            $beforeChildren = [];
            $em = $this->em;

            $builder
                ->addEventListener(FormEvents::POST_SET_DATA, function (FormEvent $event) use (&$beforeChildren) {
                    $beforeChildren = $event->getData()->toArray();
                })
                ->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event) use (&$beforeChildren, $em) {
                    $form = $event->getForm();
                    $parent = $form->getParent()->getData();
                    $currentChildren = $event->getData()->toArray();

                    // delete removed children
                    foreach ($beforeChildren as $child) {
                        if (!$parent->{'get' . ucfirst($form->getName())}()->contains($child))
                            $em->remove($child);
                    }

                    //persist added items
                    foreach ($currentChildren as $child) {
                        if ($child->getId() == null) {
                            $child->{'set' . ucfirst($form->getParent()->getName())}($parent);
                            $em->persist($child);
                        }
                    }
                });
        }
    }

    public function getBlockPrefix() : string
    {
        return 'pouyasoft_collection';
    }
}
