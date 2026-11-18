<?php

use Kirby\Content\Field;
use Kirby\Toolkit\Str;
use Wagnerwagner\Site\ReferencePageAbstract;
use Wagnerwagner\Site\Type;

class ReferenceOptionPage extends ReferencePageAbstract
{
	public function call(): string
	{
		$type = (string)$this->type();
		if ($type === 'function') {
			$value = $this->functionSignature() . ' {}';
		} elseif ($type === 'array') {
			// The full default value follows further down the page
			$value = '[]';
		} else {
			$value = $this->defaultValue();
		}
		return "'" . $this->key() . "'" . ' => ' . $value . ',';
	}

	/**
	 * Returns the default value as written in the config file of Merx
	 */
	public function defaultValue(): string|null
	{
		if ($this->content()->get('defaultValue')->exists()) {
			return $this->content()->get('defaultValue');
		}
		return null;
	}

	/**
	 * Returns the signature of a default value that is a function,
	 * e.g. `function (OrderPage $virtualOrderPage): int`
	 */
	public function functionSignature(): string
	{
		$reflection = $this->reflection();

		if ($reflection instanceof ReflectionFunction === false) {
			return 'function ()';
		}

		// Class names without namespace, as written in the config file
		$typeName = fn (ReflectionType|null $type): string => preg_replace('/(?:\w+\\\\)+(\w+)/', '$1', (string)$type);

		$parameters = array_map(
			fn (ReflectionParameter $parameter) => ltrim($typeName($parameter->getType()) . ' $' . $parameter->getName()),
			$reflection->getParameters(),
		);

		$signature = 'function (' . implode(', ', $parameters) . ')';

		if ($reflection->hasReturnType() === true) {
			$signature .= ': ' . $typeName($reflection->getReturnType());
		}

		return $signature;
	}

	public function line(): int|null
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

	public function reflection(): Reflector|null
	{
		return $this->content()->reflection()->value();
	}

	/**
	 * Returns the framework- or plugin-relative file path with the start line
	 * appended as an anchor (e.g. `config/config.php#L6`).
	 */
	public function relativeFilePath(): string|null
	{
		$root = $this->kirby()->plugin('wagnerwagner/merx')->root() . '/';
		$configFile = $root . 'config/config.php';

		if (file_exists($configFile) === false) {
			return null;
		}

		$relativeFilePath = Str::replace($configFile, $root, '');

		return $relativeFilePath;
	}

	public function title(): Field
	{
		$key = $this->key()->value();
		return parent::title()->value(Str::replace($key, 'wagnerwagner.merx.', ''));
	}

	public function type(): Type|null
	{
		return new Type($this->content()->type()->value());
	}
}
