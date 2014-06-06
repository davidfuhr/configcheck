<?php

require __DIR__ . '/../vendor/autoload.php';

use Knid\Configcheck\MysqlSetting;
use Knid\Configcheck\PhpIniFlag;
use Knid\Configcheck\PhpIniValue;
use Knid\Configcheck\SettingValidator;
use Knid\Configcheck\StringFormatter;

$app = new Silex\Application();

$app['debug'] = true;

$app->register(
    new Silex\Provider\TwigServiceProvider(), array(
        'twig.path' => __DIR__ . '/../views',
    )
);

$app->get(
    '/', function () use ($app) {
        //$pdo = new PDO('mysql:host=127.0.0.1', 'root', '');

        // init settings
        $settingValidators = array(
            // production
            new SettingValidator(new PhpIniFlag('display_errors'), false),
            new SettingValidator(new PhpIniFlag('display_startup_errors'), false),
            new SettingValidator(new PhpIniFlag('log_errors'), true),
            // general and security
            new SettingValidator(new PhpIniFlag('register_globals'), false),
            new SettingValidator(new PhpIniFlag('magic_quotes_gpc'), false),
            new SettingValidator(new PhpIniFlag('short_open_tag'), false),
            // localization
            new SettingValidator(new PhpIniValue('iconv.internal_encoding'), 'UTF-8'),
            new SettingValidator(new PhpIniValue('mbstring.internal_encoding'), 'UTF-8'),
            new SettingValidator(new PhpIniValue('date.timezone'), 'UTC'),
            /*
            // db server character set
            new SettingValidator(new MysqlSetting('character_set_server', $pdo), 'utf8'),
            new SettingValidator(new MysqlSetting('character_set_database', $pdo), 'utf8'),
            new SettingValidator(new MysqlSetting('character_set_connection', $pdo), 'utf8'),
            new SettingValidator(new MysqlSetting('character_set_client', $pdo), 'utf8'),
            new SettingValidator(new MysqlSetting('character_set_results', $pdo), 'utf8'),
            new SettingValidator(new MysqlSetting('character_set_system', $pdo), 'utf8'),
            new SettingValidator(new MysqlSetting('character_set_filesystem', $pdo), 'binary'),
            // db server collation
            new SettingValidator(new MysqlSetting('collation_server', $pdo), 'utf8_unicode_ci'),
            new SettingValidator(new MysqlSetting('collation_database', $pdo), 'utf8_unicode_ci'),
            new SettingValidator(new MysqlSetting('collation_connection', $pdo), 'utf8_unicode_ci'),
            // db server time zone
            new SettingValidator(new MysqlSetting('time_zone', $pdo), 'UTC'),
            // sql mode
            new SettingValidator(new MysqlSetting('sql_mode', $pdo), 'TRADITIONAL'),
            */
        );
        $valueFormatter = new StringFormatter();

        // process settings
        $output = array();
        foreach ($settingValidators as $settingValidator) {
            $output[] = array(
                'name'           => $settingValidator->getSetting()->getName(),
                'value'          => $valueFormatter->formatValue($settingValidator->getSetting()->getValue()),
                'is_valid'       => $settingValidator->isValid(),
                'expected_value' => $valueFormatter->formatValue($settingValidator->getExpectedValue()),
            );
        }

        return $app['twig']->render('index.html.twig', array('output' => $output));
    }
);

$app->run();
