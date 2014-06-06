<?php

namespace Knid\Configcheck;

class PhpIniFlag extends PhpIniValue
{
    public function getValue()
    {
        return (bool)parent::getValue();
    }
}
