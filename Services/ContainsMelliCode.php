<?php
namespace PouyaSoft\AppzaBundle\Services;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class ContainsMelliCode extends Constraint
{
    public $message = 'این مقدار معتبر نیست.';

    public function validatedBy()
    {
        return get_class($this).'Validator';
    }
}