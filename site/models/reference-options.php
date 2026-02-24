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

			if (is_string($defaultValue)) {
				$content['defaultValue'] = "'" . (string)$defaultValue . "'";
			} else if (is_bool($defaultValue)) {
				$content['defaultValue'] = $defaultValue ? 'true' : 'false';
			} else if (is_array($defaultValue) && count($defaultValue) === 0) {
				$content['defaultValue'] = '[]';
			} else if (is_callable($defaultValue)) {
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

	/**
	 * Returns the plugin-relative file path for options
	 * (e.g. `config/config.php`).
	 */
	public function relativeFilePath(): ?string
	{
		$root = $this->kirby()->plugin('wagnerwagner/merx')->root() . '/';
		$configFile = $root . 'config/config.php';

		if (file_exists($configFile) === false) {
			return null;
		}

		return 'config/config.php';
	}
}

