<?php

namespace PouyaSoft\AppzaBundle\Services;

use DateTimeInterface;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Cell\IValueBinder;
use PhpOffice\PhpSpreadsheet\RichText\RichText;
use PhpOffice\PhpSpreadsheet\Shared\StringHelper;

class ExcelValueBinder implements IValueBinder
{
    /**
     * Bind value to a cell.
     *
     * @param Cell $cell Cell to bind value to
     * @param mixed $value Value to bind in cell
     *
     * @return bool
     */
    public function bindValue(Cell $cell, $value)
    {
        // sanitize UTF-8 strings
        if (is_string($value)) {
            $value = StringHelper::sanitizeUTF8($value);
        } elseif (is_object($value)) {
            // Handle any objects that might be injected
            if ($value instanceof DateTimeInterface) {
                $value = $value->format('Y-m-d H:i:s');
            } elseif (!($value instanceof RichText)) {
                $value = (string) $value;
            }
        }

        // Set value explicit
        $cell->setValueExplicit($value, static::dataTypeForValue($value));

        // Done!
        return true;
    }

    /**
     * DataType for value.
     *
     * @param mixed $pValue
     *
     * @return string
     */
    public static function dataTypeForValue($pValue)
    {
        // Match the value against a few data types
        if ($pValue === null) {
            return DataType::TYPE_NULL;
        } elseif ($pValue === '') {
            return DataType::TYPE_STRING;
        } elseif ($pValue instanceof RichText) {
            return DataType::TYPE_INLINE;
        } elseif ($pValue[0] === '=' && strlen($pValue) > 1) {
            return DataType::TYPE_FORMULA;
        } elseif (is_bool($pValue)) {
            return DataType::TYPE_BOOL;
        } elseif (is_float($pValue) || is_int($pValue)) {
            return DataType::TYPE_NUMERIC;
        } elseif (is_string($pValue)) {
            $errorCodes = DataType::getErrorCodes();
            if (isset($errorCodes[$pValue])) {
                return DataType::TYPE_ERROR;
            }
        }

        return DataType::TYPE_STRING;
    }
}
