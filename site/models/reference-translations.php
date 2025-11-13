<?php

use Kirby\Cms\Pages;
use Kirby\Template\Template;
use Kirby\Toolkit\Str;
use Wagnerwagner\Site\ReferencePageAbstract;

class ReferenceTranslationsPage extends ReferencePageAbstract
{
	public function children(): Pages
  {
    if ($this->children !== null) {
			return $this->children;
		}

    $translations = $this->kirby()->plugin('ww/merx')->extends()['translations'];
    $enTranslations = $translations['en'] ?? [];
    $children = [];
    foreach ($enTranslations as $key => $value) {
			$slug = Str::slug($key);
      $children[] = [
        'slug' => $slug,
        'model' => 'reference-translation',
        'template' => 'reference-doc',
        'num' => 0,
        'content' => [
          'title' => $key,
          'key' => $key,
        ],
      ];
    }
    return $this->children = Pages::factory($children, $this);
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
	 * Returns the plugin-relative file path for translations
	 * (e.g. `translations/en.php`).
	 */
	public function relativeFilePath(): ?string
	{
		return 'translations/';
	}
}

