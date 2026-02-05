<?php

use Kirby\Cms\Pages;
use Kirby\Content\Field;
use Kirby\Toolkit\A;
use Kirby\Toolkit\Str;
use Wagnerwagner\Site\ReferencePageAbstract;
use PHPStan\PhpDocParser\Ast\Node;
use PHPStan\PhpDocParser\Ast\PhpDoc\PhpDocTextNode;

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

			$name = $method->getName();
			$className = A::last(Str::split($this->class(), '\\'));
			$reflection = new ReflectionMethod($this->class()->value(), $name);

			$title = (string)$name . '()';
			if ($reflection->isConstructor()) {
				$title = 'new ' . $className . '()';
			} else if ($reflection->isStatic()) {
				$title = $className . '::' . $title;
			} else {
				$objectName = lcfirst($className);
				$title = '$' . $objectName . '->' . $title;
			}

			$content = array_merge($content, [
				'title' => $title,
				'name' => $name,
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
				'model'    => 'reference-class-method',
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

	public function summary(): ?string
	{
		$nodes = array_filter(
			$this->docBlock()->children ?? [],
			fn (Node $node) => $node instanceof PhpDocTextNode
		);
		$node = $nodes[0] ?? null;

		if ($node instanceof PhpDocTextNode) {
			$text = explode(PHP_EOL . PHP_EOL, $node->text)[0];
			$text = str_replace(PHP_EOL, ' ', $text);
			return trim($text);
		}

		return null;
	}

	public function reflection(): ReflectionClass
	{
		return new ReflectionClass($this->class()->value());
	}

  public function line(): ?int
  {
    // return null to return file path without line number
    return null;
  }

	public function description(): ?string
	{
		$nodes = array_filter(
			$this->docBlock()->children ?? [],
			fn (Node $node) => $node instanceof PhpDocTextNode
		);
		$node = $nodes[0] ?? null;

		if ($node instanceof PhpDocTextNode) {
			$text = explode(PHP_EOL . PHP_EOL, $node->text)[1] ?? '';
			return empty($text) ? null : trim($text);
		}

		return null;
	}
}
