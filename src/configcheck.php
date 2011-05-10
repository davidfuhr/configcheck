<?php

ini_set('display_errors', 1);

interface Setting
{
	public function getName();
	public function getValue();
}

class MysqlSetting implements Setting
{
    private $name;
    private $pdoConnection;

    public function __construct($name, PDO $pdoConnection)
    {
        $this->name = (string) $name;
        $this->pdoConnection = $pdoConnection;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getValue()
    {
        $value = null;
        $stmt = $this->pdoConnection->query('SHOW variables LIKE ' . $this->pdoConnection->quote($this->name) .';');
        if ($stmt->rowCount() === 1) {
            $value = array_pop($stmt->fetch(PDO::FETCH_NUM));
        }
        return $value;
    }
}

class IniSetting implements Setting
{
	private $name;
	
	public function __construct($name)
	{
		$this->name = (string) $name;
	}
	
	public function getName()
	{
		return $this->name;
	}
	
	public function getValue()
	{
		return ini_get($this->name);
	}
}

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
	new SettingValidator(new IniSetting('display_errors'), ''),
	new SettingValidator(new IniSetting('display_startup_errors'), ''),
    // general and security
    new SettingValidator(new IniSetting('register_globals'), ''),
    new SettingValidator(new IniSetting('magic_quotes_gpc'), ''),
	// localization
	new SettingValidator(new IniSetting('iconv.internal_encoding'), 'UTF-8'),
	new SettingValidator(new IniSetting('mbstring.internal_encoding'), 'UTF-8'),

    // db server character set
    new SettingValidator(new MysqlSetting('character_set_server', $pdo), 'utf8'),
    new SettingValidator(new MysqlSetting('character_set_database', $pdo), 'utf8'),
    new SettingValidator(new MysqlSetting('character_set_connection', $pdo), 'utf8'),
    new SettingValidator(new MysqlSetting('character_set_client', $pdo), 'utf8'),
    new SettingValidator(new MysqlSetting('character_set_results', $pdo), 'utf8'),
    new SettingValidator(new MysqlSetting('character_set_system', $pdo), 'utf8'),
    new SettingValidator(new MysqlSetting('character_set_filesystem', $pdo), 'binary'),

    // db server collation
    new SettingValidator(new MysqlSetting('collation_server', $pdo), 'utf8_general_ci'),
    new SettingValidator(new MysqlSetting('collation_database', $pdo), 'utf8_general_ci'),
    new SettingValidator(new MysqlSetting('collation_connection', $pdo), 'utf8_general_ci'),
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
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
<head>
    <title>configcheck</title>
</head>
<body>
<dl>
<?php foreach ($output as $settingOutput) : ?>
	<dt><?php echo htmlspecialchars($settingOutput['name']) ?></dt>
	<dd style="color:<?php echo $settingOutput['is_valid'] ? 'green' : 'red' ?>">
		<div><?php echo htmlspecialchars($settingOutput['value']) ?></div>
		<?php if (!$settingOutput['is_valid']) : ?>
		<div>Expected value is <?php echo htmlspecialchars($settingOutput['expected_value']); ?></div>
		<?php endif; ?>
	</dd>
<?php endforeach; ?>
</dl>
</body>
</html>

