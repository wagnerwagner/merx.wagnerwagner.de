<?php

use Kirby\Filesystem\F;
use Kirby\Toolkit\A;
use Kirby\Toolkit\Str;
use PHPStan\PhpDocParser\Ast\Attribute;
use PHPStan\PhpDocParser\Ast\Node;
use PHPStan\PhpDocParser\Ast\PhpDoc\MethodTagValueNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\MethodTagValueParameterNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\PhpDocTextNode;
use Wagnerwagner\Site\ReferencePageAbstract;
use Wagnerwagner\Site\Type;
use Wagnerwagner\Site\Types;

/**
 * @method \Kirby\Content\Field name()
 */
class ReferenceClassMethodPage extends ReferencePageAbstract
{
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

	public function class(): Type
	{
		/** @var ReferenceClassPage */
		$parent = $this->parent();
		return new Type($parent->class()->toString());
	}

	public function declaringClass(): Type
	{
		$reflection = $this->reflection();

		// `@method` tags are declared by the class itself
		if ($reflection === null) {
			return $this->class();
		}

		return new Type($reflection->getDeclaringClass()->getName());
	}

	public function description(): string|null
	{
		if ($tag = $this->methodTag()) {
			$text = explode(PHP_EOL . PHP_EOL, trim($tag->description))[1] ?? '';
			return empty($text) ? null : trim($text);
		}

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

	public function line(): int|null
	{
		$tag = $this->methodTag();

		if ($tag === null) {
			return parent::line();
		}

		/** @var ReferenceClassPage */
		$parent     = $this->parent();
		$reflection = $parent->reflection();
		$docBlock   = $reflection->getDocComment();
		$tagLine    = $tag->getAttribute(Attribute::START_LINE);
		$contents   = F::read($reflection->getFileName());

		if ($docBlock === false || is_int($tagLine) === false || is_string($contents) === false) {
			return null;
		}

		$offset = strpos($contents, $docBlock);

		if ($offset === false) {
			return null;
		}

		// Line the DocBlock starts on, plus the tag’s line within the DocBlock
		return substr_count($contents, "\n", 0, $offset) + $tagLine;
	}

	/**
	 * Returns the `@method` tag this page is built from.
	 *
	 * Methods that are handled by `__call()` are invisible to reflection and
	 * are declared as `@method` tags in the DocBlock of their class instead.
	 */
	public function methodTag(): MethodTagValueNode|null
	{
		/** @var ReferenceClassPage */
		$parent = $this->parent();
		$name   = (string)$this->name();

		return A::find(
			$parent->docBlock()?->getMethodTagValues() ?? [],
			fn (MethodTagValueNode $tag) => $tag->methodName === $name,
		);
	}

	/**
	 * Turns a DocBlock type expression into the pipe-separated list
	 * `Types` expects.
	 */
	protected static function normalizeTypes(string $types): string
	{
		return Str::replace($types, ['(', ')', ' | '], ['', '', '|']);
	}

	/**
	 * Returns the parameters of the method.
	 *
	 * For `@method` tags they are read from the tag, as there are no
	 * reflected parameters to fall back to.
	 */
	public function params(): array|null
	{
		$tag = $this->methodTag();

		if ($tag === null) {
			return parent::params();
		}

		return A::map($tag->parameters, fn (MethodTagValueParameterNode $param) => [
			'name' => ($param->isVariadic ? '...' : '') . $param->parameterName,
			'types' => new Types($param->type !== null ? (string)$param->type : []),
			'defaultValue' => $param->defaultValue !== null ? (string)$param->defaultValue : null,
			'description' => null,
		]);
	}

	public function reflection(): ReflectionMethod|null
	{
		$class = (string)$this->class();
		$name  = (string)$this->name();

		// Methods declared via `@method` have nothing to reflect
		if (method_exists($class, $name) === false) {
			return null;
		}

		return new ReflectionMethod($class, $name);
	}

	/**
	 * `@method` tags live in the DocBlock of their class, so their source
	 * link points at the class file.
	 */
	public function relativeFilePath(): string|null
	{
		if ($this->methodTag() === null) {
			return parent::relativeFilePath();
		}

		/** @var ReferenceClassPage */
		$parent = $this->parent();
		return $parent->relativeFilePath();
	}

	public function returnTypes(): Types|null
	{
		$tag = $this->methodTag();

		if ($tag !== null) {
			if ($tag->returnType === null) {
				return null;
			}

			return new Types(static::normalizeTypes((string)$tag->returnType));
		}

		$reflection = $this->reflection();
		/** @var \PHPStan\PhpDocParser\Ast\PhpDoc\ReturnTagValueNode[] */
		$returnTags = $this->docBlock()?->getReturnTagValues() ?? [];
		if (isset($returnTags[0])) {
			return new Types(static::normalizeTypes((string)$returnTags[0]->type), $reflection);
		}

		if ($reflection?->getReturnType() !== null) {
			return new Types($reflection->getReturnType(), $reflection);
		}

		return null;
	}

	public function summary(): string|null
	{
		if ($tag = $this->methodTag()) {
			$text = explode(PHP_EOL . PHP_EOL, trim($tag->description))[0];
			$text = trim(str_replace(PHP_EOL, ' ', $text));
			return $text === '' ? null : $text;
		}

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
}
