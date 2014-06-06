<?php

namespace Knid\Configcheck;

class PhpIniValue implements Setting
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var string $name
     */
    public function __construct($name)
    {
        $this->name = (string) $name;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return scalar
     */
    public function getValue()
    {
        return ini_get($this->name);
    }
}

