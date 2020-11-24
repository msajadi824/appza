<?php
namespace PouyaSoft\AppzaBundle\Services;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class ContainsMelliCodeValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        if (
            !(strlen($value) == 10 && $this->checkMelliCode($value)) &&
            !(strlen($value) == 11 && $this->checkNationalCode($value)) &&
            !(strlen($value) == 24 && $this->checkShabaCode($value)) &&
            $value != ''
        ) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ value }}', $this->formatValue($value))
                ->setParameter('%string%', $value)
                ->addViolation();
        }
    }

    public static function checkMelliCode($codeMelli)
    {
        if (strlen($codeMelli) != 10)
            return false;

        if (!ctype_digit($codeMelli . ''))
            return false;

        if ($codeMelli == '0000000000' ||
            $codeMelli == '1111111111' ||
            $codeMelli == '2222222222' ||
            $codeMelli == '3333333333' ||
            $codeMelli == '4444444444' ||
            $codeMelli == '5555555555' ||
            $codeMelli == '6666666666' ||
            $codeMelli == '7777777777' ||
            $codeMelli == '8888888888' ||
            $codeMelli == '9999999999'
        )
            return false;

        $c = intval(substr($codeMelli, 9, 1));

        $n = intval(substr($codeMelli, 0, 1)) * 10 +
            intval(substr($codeMelli, 1, 1)) * 9 +
            intval(substr($codeMelli, 2, 1)) * 8 +
            intval(substr($codeMelli, 3, 1)) * 7 +
            intval(substr($codeMelli, 4, 1)) * 6 +
            intval(substr($codeMelli, 5, 1)) * 5 +
            intval(substr($codeMelli, 6, 1)) * 4 +
            intval(substr($codeMelli, 7, 1)) * 3 +
            intval(substr($codeMelli, 8, 1)) * 2;

        $r = $n - intval($n / 11) * 11;

        return (($r == 0 && $r == $c) || ($r == 1 && $c == 1) || ($r > 1 && $c == 11 - $r));
    }

    public static function checkNationalCode($nationalCode)
    {
        if (strlen($nationalCode) != 11)
            return false;

        if (!ctype_digit($nationalCode . ''))
            return false;

        $c = intval(substr($nationalCode, 10, 1));

        $dahganPlusTwo = intval(substr($nationalCode, 9, 1)) + 2;

        $n= (intval(substr($nationalCode, 0, 1)) + $dahganPlusTwo) * 29 +
            (intval(substr($nationalCode, 1, 1)) + $dahganPlusTwo) * 27 +
            (intval(substr($nationalCode, 2, 1)) + $dahganPlusTwo) * 23 +
            (intval(substr($nationalCode, 3, 1)) + $dahganPlusTwo) * 19 +
            (intval(substr($nationalCode, 4, 1)) + $dahganPlusTwo) * 17 +
            (intval(substr($nationalCode, 5, 1)) + $dahganPlusTwo) * 29 +
            (intval(substr($nationalCode, 6, 1)) + $dahganPlusTwo) * 27 +
            (intval(substr($nationalCode, 7, 1)) + $dahganPlusTwo) * 23 +
            (intval(substr($nationalCode, 8, 1)) + $dahganPlusTwo) * 19 +
            (intval(substr($nationalCode, 9, 1)) + $dahganPlusTwo) * 17;

        $r = $n - intval($n / 11) * 11;

        if($r == 10) $r = 0;

        return $r == $c;
    }

    public static function checkShabaCode($shabaCode)
    {
//        if (strlen($shabaCode) != 24)
//            return false;
//
//        if (!ctype_digit($shabaCode . ''))
//            return false;
//
//        $numberString = substr($shabaCode, 2).'1827'.substr($shabaCode, 0, 2);
//
//        return $numberString % 97 === 1;
        return true;
    }
}