<?php

namespace App\Services;

class SqlDialect
{
    /** @var string */
    private $driver;

    public function __construct($driver)
    {
        $this->driver = $driver === 'mysql' ? 'mysql' : 'sqlite';
    }

    public function driver()
    {
        return $this->driver;
    }

    public function isMysql()
    {
        return $this->driver === 'mysql';
    }

    /**
     * @param string $column
     * @return string
     */
    public function dateExpr($column)
    {
        if ($this->isMysql()) {
            return 'DATE(' . $column . ')';
        }

        return 'date(' . $column . ')';
    }

    /**
     * @param string $column
     * @return string
     */
    public function hourExpr($column)
    {
        if ($this->isMysql()) {
            return "DATE_FORMAT($column, '%H:00')";
        }

        return 'strftime("%H:00", ' . $column . ')';
    }

    /**
     * SQL condition: column >= start of range (last N days including today).
     *
     * @param string $column
     * @param int $days
     * @return string
     */
    public function sinceDaysCondition($column, $days)
    {
        $days = max(1, (int) $days) - 1;

        if ($this->isMysql()) {
            return $column . ' >= DATE_SUB(NOW(), INTERVAL ' . $days . ' DAY)';
        }

        return $column . ' >= datetime("now", "-' . $days . ' days")';
    }

    /**
     * SQL condition: column is today.
     *
     * @param string $column
     * @return string
     */
    public function isTodayCondition($column)
    {
        if ($this->isMysql()) {
            return 'DATE(' . $column . ') = CURDATE()';
        }

        return 'date(' . $column . ') = date("now")';
    }
}
