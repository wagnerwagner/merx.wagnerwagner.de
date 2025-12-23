<?php

use Kirby\Cms\Pages;
use Kirby\Template\Template;
use Kirby\Toolkit\Str;
use Wagnerwagner\Site\ReferencePageAbstract;

/**
 * @method \Kirby\Content\Field name()
 */
class ReferenceFieldMethodsPage extends ReferencePageAbstract
{
	public function children(): Pages
	{
		if ($this->children !== null) {
			return $this->children;
		}

		$fieldMethods = $this->kirby()->plugin('wagnerwagner/merx')->extends()['fieldMethods'];
		$children = [];
		foreach ($fieldMethods as $key => $value) {
			$slug = Str::slug(Str::camelToKebab($key));
			$reflectionFunction = new ReflectionFunction($value);
			$children[] = [
				'slug' => $slug,
				'model' => 'reference-field-method',
				'template' => 'reference-doc',
				'num' => 0,
				'content' => [
					'title' => $key,
					'key' => $key,
					'reflection' => $reflectionFunction,
					'returnType' => $reflectionFunction->getReturnType(),
				],
			];
		}
		return $this->children = Pages::factory($children, $this)->sort(
			'slug',
			'asc',
			SORT_NATURAL,
		);
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
