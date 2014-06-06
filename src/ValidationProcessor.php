<?php

namespace Knid\Configcheck;

class ValidationProcessor
{
    public function process($validators, $valueFormatter)
    {
        $output = array();

        foreach ($validators as $settingValidator) {
            $output[] = array(
                'name'           => $settingValidator->getSetting()->getName(),
                'value'          => $valueFormatter->formatValue($settingValidator->getSetting()->getValue()),
                'is_valid'       => $settingValidator->isValid(),
                'expected_value' => $valueFormatter->formatValue($settingValidator->getExpectedValue()),
            );
        }

        return $output;
    }
}
