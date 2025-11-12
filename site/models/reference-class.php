<?php

use Kirby\Cms\Page;
use Kirby\Cms\Pages;
use Kirby\Content\Field;
use Kirby\Toolkit\Str;

/**
 * @method static \Kirby\Content\Field class()
 */
class ReferenceClassPage extends Page
{
	public function title(): Field
	{
		$class = $this->class();
		$parts = explode('\\', $class);
		$title = end($parts);
		return $this->class()->value($title);
	}

	/**
	 * @return \Kirby\Cms\Pages<ReferenceClassMethodPage>
	 */
	public function children(): Pages
	{
		if ($this->children !== null) {
			return $this->children;
		}

		$children = [];
		$pages    = parent::children();
		$methods  = $this->reflection()->getMethods();

		foreach ($methods as $method) {
			// Don't include protected or private methods
			if ($method->isPublic() === false) {
				continue;
			}


			$slug    = Str::kebab($method->getName());
			$isMagic = substr($slug, 0, 1) === '_';
			$num     = $isMagic ? null : 1;
			$content = $pages->find($slug)?->content()->toArray() ?? [];
			$content = array_merge($content, [
				'name' => $method->getName(),
			]);

			// Ensure that constructor method is listed,
			// while other magic methods remain unlisted
			if ($slug === '__construct') {
				$num = 0;
			}

			if (!Str::startsWith($method->class, 'Wagnerwagner\\Merx')) {
				$num = null;
			}

			$children[] = [
				'slug'     => $slug,
				'model'    => 'reference-classmethod',
				'template' => 'reference-classmethod',
				'parent'   => $this,
				'content'  => $content,
				'num'      => $num
			];
		}

		// Create the actual class methods as children pages collection
		return $this->children = Pages::factory($children, $this)->sortBy(
			'isMagic',
			'desc',
			'slug',
			'asc',
			SORT_NATURAL
		);
	}


	public function reflection(): ReflectionClass
	{
		return new ReflectionClass($this->class()->value());
	}

	public function save(?array $data = null, ?string $languageCode = null, bool $overwrite = false): static
	{
		return parent::save($data, $languageCode, $overwrite);
	}
}
