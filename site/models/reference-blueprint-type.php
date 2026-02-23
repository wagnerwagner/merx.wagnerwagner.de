<?php

use Kirby\Cms\Pages;
use Kirby\Content\Field;
use Kirby\Data\Data;
use Kirby\Filesystem\Dir;
use Kirby\Toolkit\Str;
use Wagnerwagner\Site\ReferencePageAbstract;

class ReferenceBlueprintTypePage extends ReferencePageAbstract
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
		$root = $this->kirby()->plugin('wagnerwagner/merx')->root() . '/blueprints/' . $this->slug();

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
		foreach (Dir::files($root) as $filename) {
			$slug = basename($filename, '.yml');
			$root = $this->root() . '/0_' . $slug;

			try {
				$content = Data::read($root . '/reference-doc.txt');
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
					'title' => $filename,
					'filename' => $filename,
				]
			];
		}

		// Keep the raw array because Pages::factory() expects the data
		// structure in array form.
		return $children;
	}

	/**
	 * Returns the plugin-relative file path for blueprint types
	 * (e.g. `blueprints/fields`).
	 */
	public function relativeFilePath(): ?string
	{
		return 'blueprints/' . $this->slug();
	}
}
