<?php

class PhpFlagSetting extends PhpIniSetting
{
    public function getValue()
    {
        return (bool)parent::getValue();
    }
}
