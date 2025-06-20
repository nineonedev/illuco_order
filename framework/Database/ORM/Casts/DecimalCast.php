<?php

namespace Framework\Database\ORM\Casts;

class DecimalCast implements CastInterface
{
    /**
     * @var int
     */
    protected $precision;

    /**
     * @var int
     */
    protected $scale;

    /**
     * DecimalCast constructor.
     *
     * @param int $precision 총 자릿수 (예: 10)
     * @param int $scale 소수점 이하 자릿수 (예: 2)
     */
    public function __construct(int $precision = 10, int $scale = 2)
    {
        $this->precision = $precision;
        $this->scale = $scale;
    }

    /**
     * Convert value to string for DB storage
     *
     * @param mixed $value
     * @return string
     */
    public function set($value)
    {
        return number_format((float) $value, $this->scale, '.', '');
    }

    /**
     * Convert value from DB to PHP string
     *
     * @param mixed $value
     * @return string
     */
    public function get($value)
    {
        return number_format((float) $value, $this->scale, '.', '');
    }
}
