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
		} else {
			$value = $this->defaultValue() ?? ($type === 'array' ? '[]' : null);
		}
		return "'" . $this->key() . "'" . ' => ' . $value . ',';
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
			$reflection->getParameters()
		);

		$signature = 'function (' . implode(', ', $parameters) . ')';

		if ($reflection->hasReturnType() === true) {
			$signature .= ': ' . $typeName($reflection->getReturnType());
		}

		return $signature;
	}

	public function defaultValue(): string|null
	{
		if ($this->content()->get('defaultValue')->exists()) {
			return $this->content()->get('defaultValue');
		}
		return $this->functionSource();
	}

	/**
	 * Returns the source code of a default value that is a function,
	 * as written in the config file of Merx
	 */
	public function functionSource(): string|null
	{
		$reflection = $this->reflection();

		if (
			$reflection instanceof ReflectionFunction === false ||
			$reflection->getFileName() === false
		) {
			return null;
		}

		$lines = file($reflection->getFileName());

		if ($lines === false) {
			return null;
		}

		$start  = $reflection->getStartLine();
		$lines  = array_slice($lines, $start - 1, $reflection->getEndLine() - $start + 1);
		$source = implode('', $lines);

		// Removes the indentation of the first line from all lines
		preg_match('/^\s*/', $lines[0], $indentation);
		$source = preg_replace('/^' . preg_quote($indentation[0], '/') . '/m', '', $source);

		// Removes the option key before and the comma after the function
		$source = preg_replace('/^([\'"]).+?\1\s*=>\s*/', '', $source);
		$source = preg_replace('/,\s*$/', '', $source);

		return $source;
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
