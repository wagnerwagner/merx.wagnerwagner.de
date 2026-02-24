<?php

use Kirby\Cms\Page;
use Kirby\Cms\Pages;
use Kirby\Data\Data;
use Kirby\Filesystem\Dir;
use Kirby\Toolkit\Str;

class ReferenceClassesPage extends Page
{
	/**
	 * Builds the child collection for every Merx class by scanning the plugin's
	 * `src` directory and caches the result.
	 */
	public function children(): Pages
	{
		if ($this->children !== null) {
			return $this->children;
		}

		$children = [];

		// Add one child page per PHP class shipped in the plugin.
		$root = $this->kirby()->plugin('wagnerwagner/merx')->root() . '/src';

		$children = $this->childrenFactory(
			'Wagnerwagner\\Merx',
			$root,
		);

		return $this->children = Pages::factory($children, $this);
	}

	/**
	 * Returns the page configuration array for each class file found in the
	 * given namespace/root pair so `Pages::factory()` can instantiate them.
	 */
	protected function childrenFactory(
		string $namespace,
		string $root
	): array {
		$children = [];

		// Loop through each class file and register it as a child page.
		foreach (Dir::files($root) as $class) {
			$name  = ucfirst(basename($class, '.php'));
			$class = $namespace . '\\' . $name;
			$slug  = Str::kebab($name);
			$root  = $this->root() . '/' . Str::kebab(Str::replace($namespace, 'Wagnerwagner\Merx', '')) . '/0_' . $slug;

			try {
				$content = Data::read($root . '/reference-doc.txt');
			} catch (Throwable) {
				$content = [];
			}

			$reflection = new ReflectionClass($class);
			$isInternal = Str::contains($reflection->getDocComment() ?? '', '@internal');

			$children[] = [
				'slug' => $slug,
				'root' => $root,
				'model' => 'reference-class',
				'template' => 'reference-doc',
				'num' => $isInternal ? null : 0,
				'content'  => [
					...$content,
					'isInternal' => $isInternal,
					'class' => $class
				]
			];
		}

		return $children;
	}
}
