<?php

use Kirby\Cms\Page;
use Kirby\Cms\Pages;
use Kirby\Data\Data;
use Kirby\Filesystem\Dir;
use Kirby\Toolkit\Str;

class ReferenceApiPage extends Page
{
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
		$root = $this->kirby()->plugin('ww/merx')->root() . '/api';

		$children = $this->childrenFactory(
			$root,
		);

		return $this->children = Pages::factory($children, $this);
	}

	/**
	 * Creates an array of page properties for all class files in the
	 * provided root, assigning them to a provided namespace
	 */
	protected function childrenFactory(
		string $root
	): array {
			$children = [];

			// Loop through each class PHP file and
			// create as child page
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
					'template' => 'reference-api-type',
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
