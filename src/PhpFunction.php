<?php

namespace Knid\Configcheck;

class PhpFunction implements Setting
{
    /**
     * @var string
     */
    private $name;

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
        return function_exists($this->name);
    }
}
