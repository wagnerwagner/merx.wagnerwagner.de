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

    $options = $this->kirby()->plugin('ww/merx')->extends()['options'];
    $children = [];
    foreach ($options as $key => $value) {
			$slug = Str::slug($key);
      $children[] = [
        'slug' => $slug,
        'model' => 'reference-option',
        'template' => 'reference-doc',
        'num' => 0,
        'content' => [
          'key' => $key,
					'defaultValue' => $value,
        ],
      ];
    }
    return $this->children = Pages::factory($children, $this)->sortBy('num');
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
		$root = $this->kirby()->plugin('ww/merx')->root() . '/';
		$configFile = $root . 'config/config.php';

		if (file_exists($configFile) === false) {
			return null;
		}

		return 'config/config.php';
	}
}

