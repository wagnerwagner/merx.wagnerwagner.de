<?php

use Kirby\Cms\Page;
use Kirby\Cms\Pages;
use Kirby\Template\Template;
use Kirby\Toolkit\Str;
use Wagnerwagner\Site\ReferencePageAbstract;

class ReferenceHooksPage extends ReferencePageAbstract
{
	public function children(): Pages
	{
		if ($this->children !== null) {
			return $this->children;
		}

		$hooks = $this->kirby()->plugin('wagnerwagner/merx')->extends()['hooks'];
		$children = [];
		foreach ($hooks as $key => $hook) {
			if (Str::startsWith($key, 'wagnerwagner.merx.')) {
				$slug = Str::slug(Str::replace(Str::camelToKebab($key), 'wagnerwagner.merx.', ''));
				$children[] = [
					'slug' => $slug,
					'model' => 'reference-hook',
					'template' => 'reference-doc',
					'num' => 0,
					'content' => [
						'key' => $key,
						'hook' => new ReflectionFunction($hook),
					],
				];
			}
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
	 * Returns the plugin-relative file path for hooks
	 * (e.g. `config/hooks.php`).
	 */
	public function relativeFilePath(): ?string
	{
		$root = $this->kirby()->plugin('wagnerwagner/merx')->root() . '/';
		$configFile = $root . 'config/hooks.php';

		if (file_exists($configFile) === false) {
			return null;
		}

		return 'config/hooks.php';
	}
}
