<?php

interface Setting
{
	public function getName();
	public function getValue();
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

// init settings
$settingValidators = array(
	// production
	new SettingValidator(new IniSetting('display_errors'), '0'),
	new SettingValidator(new IniSetting('display_startup_errors'), ''),
	// localization
	new SettingValidator(new IniSetting('iconv.internal_encoding'), 'UTF-8'),
	new SettingValidator(new IniSetting('mbstring.internal_encoding'), 'UTF-8'),
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
?>
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
