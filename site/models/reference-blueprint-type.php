<?php

use Kirby\Cms\Page;
use Kirby\Cms\Pages;
use Kirby\Content\Field;
use Kirby\Data\Data;
use Kirby\Filesystem\Dir;
use Kirby\Toolkit\Str;

class ReferenceBlueprintTypePage extends Page
{
	public function title(): Field
	{
		$type = $this->type();
		return $this->type()->value(ucfirst($type));
	}


	/**
	 * Builds the child collection for a blueprint type by reading the
	 * plugin's blueprint directory and caching the result.
	 */
	public function children(): Pages
	{
		if ($this->children !== null) {
			return $this->children;
		}

		$children = [];

		// Add pages for every blueprint file stored for this type.
		$root = $this->kirby()->plugin('ww/merx')->root() . '/blueprints/' . $this->slug();

		$children = $this->childrenFactory(
			$root,
		);

		return $this->children = Pages::factory($children, $this);
	}

	/**
	 * Returns the page configuration array for every blueprint file in the
	 * given root directory so `Pages::factory()` can instantiate them.
	 */
	protected function childrenFactory(
		string $root
	): array {
		$children = [];

		// Loop through each blueprint file and register it as a child page.
		foreach (Dir::files($root) as $type) {
			$slug  = Str::kebab($type);
			$root  = $this->root() . '/0_' . $slug;

			try {
				$content = Data::read($root . '/reference-blueprint.txt');
			} catch (Throwable) {
				$content = [];
			}

			$children[] = [
				'slug'     => $slug,
				'root'     => $root,
				'model'    => 'reference-blueprint',
				'template' => 'reference-doc',
				'num'      => 0,
				'content'  => [
					...$content,
					'type' => $type,
				]
			];
		}

		// Keep the raw array because Pages::factory() expects the data
		// structure in array form.
		return $children;
	}
}
