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

		$translations = $this->kirby()->plugin('wagnerwagner/merx')->extends()['translations'];
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
					// The English string is the description: it says what the
					// message reads, which is what someone looking up the key
					// wants to know. `summary()` stays empty so the page itself
					// does not repeat what its translation table already shows.
					'shortdescription' => $value,
				],
			];
		}
		return $this->children = Pages::factory($children, $this)->sortBy('slug');
	}

	/**
	 * Returns the plugin-relative file path for translations
	 * (e.g. `translations/en.php`).
	 */
	public function relativeFilePath(): string|null
	{
		return 'translations/';
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
