<?php

use PHPStan\PhpDocParser\Ast\Node;
use PHPStan\PhpDocParser\Ast\PhpDoc\PhpDocTextNode;
use Wagnerwagner\Site\ReferencePageAbstract;
use Wagnerwagner\Site\Type;
use Wagnerwagner\Site\Types;

class ReferenceSiteMethodPage extends ReferencePageAbstract
{
	public function defaultValue(): string|null
	{
		$defaultValue = $this->content()->defaultValue()->value();
		if (is_array($defaultValue)) {
			return var_export($defaultValue, true);
		}
		return var_export($defaultValue, true);
	}

	public function description(): string|null
	{
		$nodes = array_filter(
			$this->docBlock()->children ?? [],
			fn (Node $node) => $node instanceof PhpDocTextNode
		);
		$node = $nodes[0] ?? null;

		if ($node instanceof PhpDocTextNode) {
			$text = explode(PHP_EOL . PHP_EOL, $node->text, 2)[1] ?? '';
			return empty($text) ? null : trim($text);
		}

		return null;
	}

	public function reflection(): ReflectionFunction|null
	{
		return $this->content()->get('reflection')?->value();
	}

	public function returnTypes(): Types|null
	{
		if (!$this->reflection()->hasReturnType()) {
			return new Types(['void']);
		}
		return new Types([$this->reflection()->getReturnType()]);
	}

	public function summary(): string|null
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

	public function type(): Type|null
	{
		return new Type(gettype($this->content()->defaultValue()->value()));
	}
}
