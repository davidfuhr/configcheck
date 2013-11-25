<?php

require 'Setting.php';
require 'PhpIniSetting.php';
require 'PhpFlagSetting.php';
require 'MysqlSetting.php';

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

$pdo = new PDO('mysql:host=127.0.0.1', 'root', '');

// init settings
$settingValidators = array(
    // production
    new SettingValidator(new PhpFlagSetting('display_errors'), false),
    new SettingValidator(new PhpFlagSetting('display_startup_errors'), false),
    new SettingValidator(new PhpFlagSetting('log_errors'), true),
    // general and security
    new SettingValidator(new PhpFlagSetting('register_globals'), false),
    new SettingValidator(new PhpFlagSetting('magic_quotes_gpc'), false),
    new SettingValidator(new PhpFlagSetting('short_open_tag'), false),
    // localization
    new SettingValidator(new PhpIniSetting('iconv.internal_encoding'), 'UTF-8'),
    new SettingValidator(new PhpIniSetting('mbstring.internal_encoding'), 'UTF-8'),
    new SettingValidator(new PhpIniSetting('date.timezone'), 'UTC'),
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
);
$valueFormatter = new StringFormatter();



// process settings
$output = array();
foreach ($settingValidators as $settingValidator) {
    $output[] = array(
        'name' => $settingValidator->getSetting()->getName(),
        'value' => $valueFormatter->formatValue($settingValidator->getSetting()->getValue()),
        'is_valid' => $settingValidator->isValid(),
        'expected_value' => $valueFormatter->formatValue($settingValidator->getExpectedValue()),
    );
}



// start output
?><!DOCTYPE html>
<html>
<head>
    <title>configcheck</title>

    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.0.2/css/bootstrap.min.css">

    <!-- Optional theme -->
    <link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.0.2/css/bootstrap-theme.min.css">

    <!-- Latest compiled and minified JavaScript -->
    <script src="//netdna.bootstrapcdn.com/bootstrap/3.0.2/js/bootstrap.min.js"></script>
</head>
<body>
<div class="container">
<dl class="dl-horizontal">
    <?php foreach ($output as $settingOutput) : ?>
        <dt><?php echo htmlspecialchars($settingOutput['name']) ?></dt>
        <dd style="color:<?php echo $settingOutput['is_valid'] ? 'green' : 'red' ?>">
            <?php echo htmlspecialchars($settingOutput['value']) ?>
            <span class="glyphicon glyphicon-<?php if ($settingOutput['is_valid']): ?>ok<?php else: ?>remove<?php endif; ?>"></span>
            <br />
            <?php if (!$settingOutput['is_valid']): ?>
                Recommended value is <?php echo htmlspecialchars($settingOutput['expected_value']); ?>
            <?php endif; ?>
        </dd>
    <?php endforeach; ?>
</dl>
</div>
<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
<script src="https://code.jquery.com/jquery.js"></script>
<!-- Include all compiled plugins (below), or include individual files as needed -->
<script src="js/bootstrap.min.js"></script>
</body>
</html>


