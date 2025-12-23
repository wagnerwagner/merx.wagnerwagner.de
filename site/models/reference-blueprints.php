<?php

use Kirby\Cms\Page;
use Kirby\Cms\Pages;
use Kirby\Data\Data;
use Kirby\Filesystem\Dir;
use Kirby\Toolkit\Str;

class ReferenceBlueprintsPage extends Page
{
	/**
	 * Builds the child collection for every blueprint type defined in the
	 * plugin and caches the result.
	 */
	public function children(): Pages
	{
		if ($this->children !== null) {
			return $this->children;
		}

		$children = [];

		// Add one child page per blueprint type directory in the plugin.
		$root = $this->kirby()->plugin('wagnerwagner/merx')->root() . '/blueprints';

		$children = $this->childrenFactory(
			$root,
		);

		return $this->children = Pages::factory($children, $this);
	}

	/**
	 * Returns the page configuration array for each blueprint type directory
	 * inside the given root, preparing it for `Pages::factory()`.
	 */
	protected function childrenFactory(
		string $root
	): array {
		$children = [];

		// Loop through each blueprint type directory and register it.
		foreach (Dir::dirs($root) as $type) {
			$slug  = Str::kebab($type);
			$root  = $this->root() . '/0_' . $slug;

			try {
				$content = Data::read($root . '/reference-blueprint-type.txt');
			} catch (Throwable) {
				$content = [];
			}

			$children[] = [
				'slug'     => $slug,
				'root'     => $root,
				'model'    => 'reference-blueprint-type',
				'template' => 'reference-doc',
				'num'      => 0,
				'content'  => [
					...$content,
					'type' => $type,
				]
			];
		}

		// Keep the raw array because Pages::factory() expects this structure.
		return $children;
	}
}
