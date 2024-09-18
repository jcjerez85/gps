<?php

namespace Tobuli\Helpers\Formatter\Unit;

use Tobuli\Helpers\Formatter\DST;

class DateTime extends Unit
{
    /**
     * @var string
     */
    protected $systemFormat = 'Y-m-d H:i:s';

    /**
     * @var string
     */
    protected $format;

    /**
     * @var DST
     */
    protected $DST;

    public function __construct(DST $DST, string $systemFormat)
    {
        $this->DST = $DST;
        $this->systemFormat = $systemFormat;
        $this->setFormat($systemFormat);
    }

    public function setFormat(string $format)
    {
        $this->format = $format;
    }

    public function getFormat(): string
    {
        return $this->format;
    }

    public function unit()
    {
        $zone = $this->DST->getZone(time());

        return 'UTC ' . str_replace(['hours', '-minutes', 'minutes'], '', $zone);
    }

    public function convert($value, $format = null)
    {
        if (! isset($format)) {
            $format = $this->systemFormat;
        }

        return date($format, $this->DST->apply($value));
    }

    public function reverse($value, $format = null)
    {
        if (! isset($format)) {
            $format = $this->systemFormat;
        }

        return date($format, $this->DST->applyReverse($value));
    }

    public function format($value, $format = null)
    {
        if (! isset($format)) {
            $format = $this->format;
        }

        return date($format, strtotime($value));
    }

    public function human($value)
    {
        if (empty($value) || substr($value, 0, 4) == '0000') {
            return '-';
        }

        return $this->convert($value, $this->format);
    }

    public function now()
    {
        return $this->timestamp(date($this->systemFormat));
    }

    public function timestamp($value)
    {
        return strtotime($this->convert($value));
    }
}
