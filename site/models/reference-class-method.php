<?php

use Kirby\Content\Field;
use Kirby\Toolkit\A;
use Kirby\Toolkit\Str;
use PHPStan\PhpDocParser\Ast\Node;
use PHPStan\PhpDocParser\Ast\PhpDoc\PhpDocTextNode;
use Wagnerwagner\Site\ReferencePageAbstract;
use Wagnerwagner\Site\Type;
use Wagnerwagner\Site\Types;

/**
 * @method \Kirby\Content\Field name()
 */
class ReferenceClassMethodPage extends ReferencePageAbstract
{
	public function title(): Field
	{
		$className = A::last(Str::split($this->class(), '\\'));
		$reflection = $this->reflection();

		$title = (string)$this->name() . '()';
		if ($reflection->isConstructor()) {
			$title = 'new ' . $className . '()';
		} else if ($reflection->isStatic()) {
			$title = $className . '::' . $title;
		} else {
			$objectName = lcfirst($className);
			$title = '$' . $objectName . '->' . $title;
		}
		return $this->name()->value($title);
	}

	public function class(): Type
	{
		/** @var ReferenceClassPage */
		$parent = $this->parent();
		return new Type($parent->class()->toString());
	}

	public function declaringClass(): Type
	{
		return new Type($this->reflection()->getDeclaringClass()->getName());
	}

	public function reflection(): ?ReflectionMethod
	{
		return new ReflectionMethod((string)$this->class(), $this->name());
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

	public function returnTypes(): ?Types
	{
		$reflection = $this->reflection();
		/** @var \PHPStan\PhpDocParser\Ast\PhpDoc\ReturnTagValueNode[] */
		$returnTags = $this->docBlock()?->getReturnTagValues() ?? [];
		if (isset($returnTags[0])) {
			return new Types((string)$returnTags[0]->type, $reflection);
		}

		if ($reflection->getReturnType() !== null) {
			return new Types($reflection->getReturnType(), $reflection);
		}

		return null;
	}

	/**
	 * Returns the DocBlock description for the method's return value as an
	 * HTML string with preserved line breaks.
	 */
	public function returnDescription(): ?string
	{
		/** @var \PHPStan\PhpDocParser\Ast\PhpDoc\ReturnTagValueNode[] */
		$returnTags = $this->docBlock()?->getReturnTagValues() ?? [];
		if (count($returnTags) === 0) {
			return null;
		}

		$description = trim($returnTags[0]->description);
		return $description === '' ? null : $description;
	}

	/**
	 * Returns an array describing every exception declared via @throws tags.
	 */
	public function exceptions(): array
	{
		/** @var ?\PHPStan\PhpDocParser\Ast\PhpDoc\ThrowsTagValueNode[] */
		$throws = $this->docBlock()?->getThrowsTagValues() ?? [];

		return array_map(fn ($tag) => [
			'type' => new Type($tag->type),
			'description' => $tag->description,
		], $throws);
	}

	/**
	 * Returns a string representation of the method signature including types,
	 * defaults and return type.
	 */
	public function call(): string
	{
		$parameters = array_map(function ($param) {
			$string = '';
			if (count($param['types']) > 0) {
				$string .= $param['types'] . ' ';
			}
			$string .= $param['name'];
			if ($param['defaultValue']) {
				$string .= ' = ' . $param['defaultValue'];

			}
			return $string;
		}, $this->params() ?? []);
		$call = substr($this->title(), 0, -2) . '(' . implode(', ', $parameters) . ')';

		if ($this->returnTypes()) {
			$call .= ': ' . $this->returnTypes();
		}

		return $call;
	}
}
