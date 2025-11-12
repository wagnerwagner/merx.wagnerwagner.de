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
	 * Creates children collection by parsing the `src/` folder of
	 * the Kirby core
	 */
	public function children(): Pages
	{
		if ($this->children !== null) {
			return $this->children;
		}

		$children = [];

		// Add unlisted pages for all classes in namespace:
		// Loop through filesystem as proxy for namespace structure
		$root = $this->kirby()->plugin('ww/merx')->root() . '/blueprints/' . $this->slug();

		$children = $this->childrenFactory(
			$root,
		);

		return $this->children = Pages::factory($children, $this);
	}

	/**
	 * Creates an array of page properties for all blueprint folders in the
	 * provided root.
	 */
	protected function childrenFactory(
		string $root
	): array {
		$children = [];

		// Loop through each class PHP file and
		// create as child page
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
				'template' => 'reference-blueprint',
				'num'      => 0,
				'content'  => [
					...$content,
					'type' => $type,
				]
			];
		}

		// we need to create a Pages collection to properly filter
		// pages (e.g. as non-internal); however, we need to pass the
		// data on as array again to be consumable by the upper
		// Pages::factory() call
		return $children;
	}
}
