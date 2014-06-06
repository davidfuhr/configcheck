<?php

namespace Knid\Configcheck;

class SettingValidator
{
    private $setting;
    private $expectedValue;

    public function __construct(Setting $setting, $expectedValue)
    {
        $this->setting = $setting;
        $this->expectedValue = $expectedValue;
    }

    public function isValid()
    {
        return $this->expectedValue === $this->setting->getValue();
    }

    public function getSetting()
    {
        return $this->setting;
    }

    public function getExpectedValue()
    {
        return $this->expectedValue;
    }
}
