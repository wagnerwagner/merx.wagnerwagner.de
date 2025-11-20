<?php

use Kirby\Cms\Page;
use Kirby\Cms\Pages;
use Kirby\Template\Template;
use Kirby\Toolkit\Str;

class ReferenceHooksPage extends Page
{
	public function children(): Pages
  {
    if ($this->children !== null) {
			return $this->children;
		}

    $hooks = $this->kirby()->plugin('ww/merx')->extends()['hooks'];
    $children = [];
    foreach ($hooks as $key => $hook) {
			$slug = Str::slug(Str::replace($key, 'ww.merx.', ''));
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
}
