<?php

use Kirby\Cms\Page;
use Kirby\Cms\Pages;
use Kirby\Data\Data;
use Kirby\Filesystem\Dir;
use Kirby\Toolkit\Str;

class ReferenceApiPage extends Page
{
	/**
	 * Builds the children collection from the plugin's `api` directory
	 * and caches it on the page instance.
	 */
	public function children(): Pages
	{
		if ($this->children !== null) {
			return $this->children;
		}

		$children = [];

		// Add unlisted pages for each available API type by mirroring
		// the directory structure inside the plugin's `api` folder.
		$root = $this->kirby()->plugin('ww/merx')->root() . '/api';

		$children = $this->childrenFactory(
			$root,
		);

		return $this->children = Pages::factory($children, $this);
	}

	/**
	 * Returns the page property array for every directory inside the
	 * given root, mapping each one to a `reference-api-type` child.
	 */
	protected function childrenFactory(
		string $root
	): array {
			$children = [];

			// Loop through every API type directory and register it
			// as a child page configuration.
			foreach (Dir::dirs($root) as $type) {
				$slug  = Str::kebab($type);
				$root  = $this->root() . '/0_' . $slug;

				try {
					$content = Data::read($root . '/reference-api-type.txt');
				} catch (Throwable) {
					$content = [];
				}

				/** exclude collections and data */
				$num = in_array($slug, ['collections', 'data']) ? null : 0;

				$children[] = [
					'slug'     => $slug,
					'root'     => $root,
					'model'    => 'reference-api-type',
					'template' => 'reference-doc',
					'num'      => $num,
					'content'  => [
						...$content,
						'type' => $type,
					]
				];
			}

			return $children;
		}
}
