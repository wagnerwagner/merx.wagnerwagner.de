<?php

use Kirby\Content\Field;
use Kirby\Toolkit\Str;
use Wagnerwagner\Site\ReferencePageAbstract;
use Wagnerwagner\Site\Type;

class ReferenceOptionPage extends ReferencePageAbstract
{
	public function title(): Field
	{
		$key = $this->key()->value();
		return parent::title()->value(Str::replace($key, 'wagnerwagner.merx.', ''));
	}

	public function type(): ?Type
	{
		return new Type($this->content()->type()->value());
	}

	public function reflection(): ?Reflector
	{
		return $this->content()->reflection()->value();
	}

	public function call(): string
	{
		$type = (string)$this->type();
		$value = $this->defaultValue();
		if ($value === null) {
			if ($type === 'function') {
				$value = 'function () {}';
			} else if ($type === 'array') {
				$value = '[]';
			}
		}
		return "'" . $this->key() . "'" . ' => ' . $value . ',';
	}

	public function defaultValue(): ?string
	{
		if ($this->content()->get('defaultValue')->exists()) {
			return $this->content()->get('defaultValue');
		}
		return null;
	}


	/**
	 * Returns the framework- or plugin-relative file path with the start line
	 * appended as an anchor (e.g. `config/config.php#L6`).
	 */
	public function relativeFilePath(): ?string
	{
		$root = $this->kirby()->plugin('wagnerwagner/merx')->root() . '/';
		$configFile = $root . 'config/config.php';

		if (file_exists($configFile) === false) {
			return null;
		}

		$relativeFilePath = Str::replace($configFile, $root, '');

		return $relativeFilePath;
	}

	public function line(): ?int
	{
		$root = $this->kirby()->plugin('wagnerwagner/merx')->root() . '/';
		$configFile = $root . 'config/config.php';

		if (file_exists($configFile) === false) {
			return null;
		}

		$lineNumber = null;
		$lines = file($configFile);

		// Try to find the line number where this option is defined
		$key = $this->key()->value();
		$keyWithoutPrefix = Str::replace($key, 'wagnerwagner.merx.', '');

		if ($lines !== false) {
			foreach ($lines as $index => $line) {
				// Look for the key in the config file
				if (preg_match('/[\'"]' . preg_quote($keyWithoutPrefix, '/') . '[\'"]/', $line)) {
					$lineNumber = $index + 1;
					break;
				}
			}
		}

		return $lineNumber;
	}
}

