<?php

namespace PouyaSoft\AppzaBundle\Form\Type;

use Symfony\Component\Form\DataTransformerInterface;

class IntegerCommaTransformer implements DataTransformerInterface
{
    public function transform($number = null)
    {
        return $number ? number_format($number, 0, '.', ',') : null;
    }

    public function reverseTransform($input)
    {
        return str_replace(',', '', $input);
    }
}