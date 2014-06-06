<?php

namespace Knid\Configcheck;

class StringFormatter
{
    public function formatValue($value)
    {
        if (is_string($value) && $value === '') {
            $value = '0';
        }
        if (is_null($value)) {
            $value = 'NULL';
        }
        if (is_bool($value)) {
            $value = $value ? 'TRUE' : 'FALSE';
        }

        return $value;
    }
}
