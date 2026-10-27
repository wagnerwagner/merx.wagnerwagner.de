<?php

use Kirby\Cms\Pages;
use Kirby\Template\Template;
use Kirby\Toolkit\Str;
use Wagnerwagner\Site\ReferencePageAbstract;

class ReferenceOptionsPage extends ReferencePageAbstract
{
	public function children(): Pages
	{
		if ($this->children !== null) {
			return $this->children;
		}

		$options = $this->kirby()->plugin('wagnerwagner/merx')->extends()['options'];
		$sources = $this->defaultValueSources();
		$children = [];
		foreach ($options as $key => $defaultValue) {
			$slug = Str::slug(Str::camelToKebab($key));

			$type = gettype($defaultValue);
			if (is_callable($defaultValue)) {
				$type = 'function';
			}

			$content = [
				'key' => 'wagnerwagner.merx.' . $key,
				'type' => $type,
			];

			if (isset($sources[$key]) === true) {
				$content['defaultValue'] = $sources[$key];
			} elseif (is_string($defaultValue)) {
				$content['defaultValue'] = "'" . (string)$defaultValue . "'";
			} elseif (is_bool($defaultValue)) {
				$content['defaultValue'] = $defaultValue ? 'true' : 'false';
			} elseif (is_array($defaultValue) && count($defaultValue) === 0) {
				$content['defaultValue'] = '[]';
			}

			if (is_callable($defaultValue)) {
				$content['reflection'] = new ReflectionFunction($defaultValue);
			}

			$children[] = [
				'slug' => $slug,
				'model' => 'reference-option',
				'template' => 'reference-doc',
				'num' => 0,
				'content' => $content,
			];
		}
		return $this->children = Pages::factory($children, $this)->sortBy('slug');
	}

	/**
	 * Returns the default value of each option as written in the config
	 * file of Merx, by option key without prefix
	 *
	 * @return array<string, string>
	 */
	public function defaultValueSources(): array
	{
		$configFile = $this->kirby()->plugin('wagnerwagner/merx')->root() . '/config/config.php';
		$contents   = is_file($configFile) === true ? file_get_contents($configFile) : false;

		if ($contents === false) {
			return [];
		}

		$tokens      = PhpToken::tokenize($contents);
		$sources     = [];
		$depth       = 0;
		$key         = null;
		$value       = null;
		$indentation = '';

		foreach ($tokens as $index => $token) {
			if (in_array($token->text, ['[', '(', '{', '${'], true) === true) {
				$depth++;
			} elseif (in_array($token->text, [']', ')', '}'], true) === true) {
				$depth--;
			}

			// Collects the value until the comma or bracket that ends it
			if ($value !== null) {
				if ($depth === 0 || ($depth === 1 && $token->text === ',')) {
					// Without the indentation of the option key
					$sources[$key] = preg_replace('/^' . preg_quote($indentation, '/') . '/m', '', trim($value));
					$value = null;
				} else {
					$value .= $token->text;
				}
				continue;
			}

			// Option keys are the strings of the returned array followed by `=>`
			if ($depth === 1 && $token->is(T_CONSTANT_ENCAPSED_STRING) === true) {
				$key = substr($token->text, 1, -1);
				$previous = $tokens[$index - 1] ?? null;
				$indentation = $previous?->is(T_WHITESPACE) === true ? substr(strrchr($previous->text, "\n") ?: "\n", 1) : '';
			} elseif ($depth === 1 && $key !== null && $token->is(T_DOUBLE_ARROW) === true) {
				$value = '';
			} elseif ($token->is([T_WHITESPACE, T_COMMENT, T_DOC_COMMENT]) === false) {
				$key = null;
			}
		}

		return $sources;
	}

	/**
	 * Returns the plugin-relative file path for options
	 * (e.g. `config/config.php`).
	 */
	public function relativeFilePath(): string|null
	{
		$root = $this->kirby()->plugin('wagnerwagner/merx')->root() . '/';
		$configFile = $root . 'config/config.php';

		if (file_exists($configFile) === false) {
			return null;
		}

		return 'config/config.php';
	}

	/**
	 * Returns the final template
	 */
	public function template(): Template
	{
		if ($this->template !== null) {
			return $this->template;
		}

		$intended = $this->intendedTemplate();

		if ($intended->exists() === true) {
			return $this->template = $intended;
		}

		return $this->template = $this->kirby()->template('reference-doc');
	}
}
