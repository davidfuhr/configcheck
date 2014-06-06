<?php

require __DIR__ . '/../vendor/autoload.php';

use Knid\Configcheck\PhpFunction;
use Knid\Configcheck\PhpIniFlag;
use Knid\Configcheck\PhpIniValue;
use Knid\Configcheck\SettingValidator;
use Knid\Configcheck\StringFormatter;
use Knid\Configcheck\ValidationProcessor;

$app = new Silex\Application();

$app['debug'] = true;

$app->register(
    new Silex\Provider\TwigServiceProvider(), array(
        'twig.path' => __DIR__ . '/../views',
    )
);

$app->get(
    '/', function () use ($app) {
        $settingValidators = array();

        $yaml = new \Symfony\Component\Yaml\Parser();
        $reqs = $yaml->parse(file_get_contents(__DIR__ . '/../etc/requirements.yml'));

        if (array_key_exists('php_ini_flag', $reqs)) {
            foreach ($reqs['php_ini_flag'] as $req) {
                list($name, $value) = $req;
                $settingValidators[] = new SettingValidator(new PhpIniFlag($name), $value);
            }
        }

        if (array_key_exists('php_ini_value', $reqs)) {
            foreach ($reqs['php_ini_value'] as $req) {
                list($name, $value) = $req;
                $settingValidators[] = new SettingValidator(new PhpIniValue($name), $value);
            }
        }

        $phpFunctionValidators = array();
        if (array_key_exists('php_function', $reqs)) {
            foreach ($reqs['php_function'] as $req) {
                $phpFunctionValidators[] = new SettingValidator(new PhpFunction($req), true);
            }
        }

        $processor = new ValidationProcessor();
        $valueFormatter = new StringFormatter();

        $configOutput = $processor->process($settingValidators, $valueFormatter);
        $phpFunctionOutput = $processor->process($phpFunctionValidators, $valueFormatter);

        return $app['twig']->render('index.html.twig', array(
                'phpConfigOutput' => $configOutput,
                'phpFunctionOutput' => $phpFunctionOutput,
            ));
    }
);

$app->run();
