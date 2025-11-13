<?php

use Kirby\Cms\Pages;
use Kirby\Content\Field;
use Kirby\Toolkit\Str;
use Wagnerwagner\Site\ReferencePageAbstract;

/**
 * @method static \Kirby\Content\Field class()
 */
class ReferenceClassPage extends ReferencePageAbstract
{
	public function title(): Field
	{
		$class = $this->class();
		$parts = explode('\\', $class);
		$title = end($parts);
		return $this->class()->value($title);
	}

	/**
	 * Returns (and caches) the public class methods as child pages.
	 *
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
				'template' => 'reference-doc',
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

	/**
	 * Returns the framework- or plugin-relative file path with the start line
	 * appended as an anchor (e.g. `src/ListItems.php#L32`).
	 */
	public function relativeFilePath(): ?string
	{
		$reflection = $this->reflection();
		$className = $reflection->getName();

		if (Str::startsWith($className, 'Kirby\\')) {
			$root = $this->kirby()->root('kirby') . '/';
		} else if (Str::startsWith($className, 'Wagnerwagner\\')) {
			$root = $this->kirby()->plugin('ww/merx')->root() . '/';
		} else {
			return null;
		}

		$fileName = $reflection->getFileName();
		if ($fileName === false) {
			return null;
		}

		$relativeFilePath = Str::replace($fileName, $root, '');
		return $relativeFilePath;
	}

  public function line(): ?int
  {
    // return null to return file path without line number
    return null;
  }

	public function save(?array $data = null, ?string $languageCode = null, bool $overwrite = false): static
	{
		return parent::save($data, $languageCode, $overwrite);
	}
}
