<?php

namespace PouyaSoft\AppzaBundle\Service;

use PouyaSoft\AppzaBundle\Lib\IntlDateTime;
use Symfony\Component\HttpFoundation\RequestStack;

class jDateService
{
    protected $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    /**
     * @param string $persian
     * @param string $format
     * @param string $locale (e.g. fa, fa_IR, en, en_US, en_UK, ...)
     * @param string $calendar (e.g. gregorian, persian, islamic, ...)
     * @return \DateTime
     */
    public function persianToGeorgian($persian, $format = 'yyyy/MM/dd', $locale = 'fa', $calendar = 'persian')
    {
        return $this->intlDateTimeInstance($persian, null, $calendar, $locale, $format);
    }

    /**
     * @param \DateTime $georgian
     * @param string $format
     * @param string $locale (e.g. fa, fa_IR, en, en_US, en_UK, ...)
     * @param string $calendar (e.g. gregorian, persian, islamic, ...)
     * @param bool $latinizeDigit
     * @return string
     */
    public function georgianToPersian($georgian = null, $format = 'yyyy/MM/dd', $locale = 'fa', $calendar = 'persian', $latinizeDigit = false)
    {
        return $georgian ? $this->intlDateTimeInstance($georgian, null, $calendar, $locale, null)->intlFormat($format, null, $latinizeDigit) : '--';
    }

    /**
     * Creates a new instance of IntlDateTime
     *
     * @param mixed $time Unix timestamp or strtotime() compatible string or another DateTime object
     * @param mixed $timezone DateTimeZone object or timezone identifier as full name (e.g. Asia/Tehran) or abbreviation (e.g. IRDT).
     * @param string $calendar any calendar supported by ICU (e.g. gregorian, persian, islamic, ...)
     * @param string $locale any locale supported by ICU (e.g. fa, fa_IR, en, en_US, en_UK, ...)
     * @param string $pattern the date pattern in which $time is formatted.
     * @return IntlDateTime
     */
    public function intlDateTimeInstance($time = null, $timezone = null, $calendar = 'persian', $locale = 'fa', $pattern = null)
    {
        return new IntlDateTime($time, $timezone, $calendar, $locale, $pattern);
    }

    /**
     * @param \DateTime $georgian
     * @param string $format
     * @param string $locale (e.g. fa, fa_IR, en, en_US, en_UK, ...)
     * @param string $calendar (e.g. gregorian, persian, islamic, ...)
     * @param bool $latinizeDigit
     * @return string
     */
    public function georgianToLocale($georgian = null, $format = 'yyyy/MM/dd', $locale = null, $calendar = null, $latinizeDigit = false)
    {
        if(!$locale)
            $locale = $this->requestStack->getCurrentRequest()->getLocale();

        if(!$calendar) {
            switch (substr($locale, 0, 2)) {
                case 'en': $calendar = 'georgian'; break;
                case 'fa': $calendar = 'persian'; break;
                case 'ar': $calendar = 'islamic'; break;
                default:   $calendar = 'georgian'; break;
            }
        }

        return $georgian ? $this->intlDateTimeInstance($georgian, null, $calendar, $locale, null)->intlFormat($format, null, $latinizeDigit) : '--';
    }
}
