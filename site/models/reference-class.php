<?php

use Kirby\Cms\Pages;
use Kirby\Content\Field;
use Kirby\Toolkit\A;
use Kirby\Toolkit\Str;
use PHPStan\PhpDocParser\Ast\Node;
use PHPStan\PhpDocParser\Ast\PhpDoc\PhpDocTextNode;
use Wagnerwagner\Site\ReferencePageAbstract;
use Wagnerwagner\Site\Types;

/**
 * @method static \Kirby\Content\Field class()
 */
class ReferenceClassPage extends ReferencePageAbstract
{
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

			$content = array_merge($content, [
				'title' => $this->methodTitle(
					$name,
					isStatic: $method->isStatic(),
					isConstructor: $method->isConstructor(),
				),
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
				'num'      => $num,
			];
		}

		// Methods that are handled by `__call()` are invisible to reflection.
		// They are declared as `@method` tags in the class DocBlock instead.
		foreach ($this->docBlock()?->getMethodTagValues() ?? [] as $tag) {
			$slug = Str::kebab($tag->methodName);

			// A real method of the same name is documented already
			if (in_array($slug, array_column($children, 'slug'), true) === true) {
				continue;
			}

			$content = $pages->find($slug)?->content()->toArray() ?? [];
			$content = array_merge($content, [
				'title' => $this->methodTitle($tag->methodName, isStatic: $tag->isStatic),
				'name' => $tag->methodName,
			]);

			$children[] = [
				'slug'     => $slug,
				'model'    => 'reference-class-method',
				'template' => 'reference-doc',
				'parent'   => $this,
				'content'  => $content,
				'num'      => 1,
			];
		}

		// Create the actual class methods as children pages collection
		return $this->children = Pages::factory($children, $this)->sortBy(
			'isMagic',
			'desc',
			'slug',
			'asc',
			SORT_NATURAL,
		);
	}

	public function description(): string|null
	{
		$nodes = array_filter(
			$this->docBlock()->children ?? [],
			fn (Node $node) => $node instanceof PhpDocTextNode,
		);
		$node = $nodes[0] ?? null;

		if ($node instanceof PhpDocTextNode) {
			$text = explode(PHP_EOL . PHP_EOL, $node->text)[1] ?? '';
			return empty($text) ? null : trim($text);
		}

		return null;
	}

	public function line(): int|null
	{
		// return null to return file path without line number
		return null;
	}

	/**
	 * Returns how a method of this class is called,
	 * e.g. `$orderPage->cart()`, `Cart::fromSession()` or `new Cart()`.
	 */
	protected function methodTitle(
		string $name,
		bool $isStatic = false,
		bool $isConstructor = false,
	): string {
		$className = A::last(Str::split($this->class(), '\\'));

		if ($isConstructor === true) {
			return 'new ' . $className . '()';
		}

		if ($isStatic === true) {
			return $className . '::' . $name . '()';
		}

		return '$' . lcfirst($className) . '->' . $name . '()';
	}

	/**
	 * The public properties the class declares itself, with the
	 * description their DocBlock carries
	 *
	 * @return array<int, array{name: string, types: Types, description: string|null}>
	 */
	public function properties(): array
	{
		$reflection = $this->reflection();
		$properties = [];

		foreach ($reflection->getProperties(ReflectionProperty::IS_PUBLIC) as $property) {
			if ($property->getDeclaringClass()->getName() !== $reflection->getName()) {
				continue;
			}

			// The summary: what stands before the first blank line or tag
			$description = preg_replace('!^\s*/?\*+/?[ \t]?|\s*\*+/$!m', '', (string)$property->getDocComment());
			$description = preg_split('!\n\s*\n|\n?@!', trim($description))[0];
			$description = trim(preg_replace('!\s+!', ' ', $description));

			$properties[] = [
				'name' => ($property->isStatic() ? 'static ' : '') . '$' . $property->getName(),
				'types' => new Types((string)($property->getType() ?? 'mixed'), $reflection),
				'description' => $description === '' ? null : $description,
			];
		}

		return $properties;
	}

	public function reflection(): ReflectionClass
	{
		return new ReflectionClass($this->class()->value());
	}

	public function summary(): string|null
	{
		$nodes = array_filter(
			$this->docBlock()->children ?? [],
			fn (Node $node) => $node instanceof PhpDocTextNode,
		);
		$node = $nodes[0] ?? null;

		if ($node instanceof PhpDocTextNode) {
			$text = explode(PHP_EOL . PHP_EOL, $node->text)[0];
			$text = str_replace(PHP_EOL, ' ', $text);
			return trim($text);
		}

		return null;
	}

	public function title(): Field
	{
		$class = $this->class();
		$parts = explode('\\', $class);
		$title = end($parts);
		return $this->class()->value($title);
	}
}
