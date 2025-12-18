<?php

use Kirby\Content\Field;
use PHPStan\PhpDocParser\Ast\Node;
use PHPStan\PhpDocParser\Ast\PhpDoc\PhpDocTextNode;
use Wagnerwagner\Site\ReferencePageAbstract;
use Wagnerwagner\Site\Type;
use Wagnerwagner\Site\Types;

class ReferenceFieldMethodPage extends ReferencePageAbstract
{
	public function title(): Field
	{
		$key = $this->key()->value();
		return parent::title()->value('$field->' . $key . '()');
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

	public function reflection(): ?ReflectionFunction
	{
		return $this->content()->get('reflection')?->value();
	}

	public function type(): ?Type
	{
		return new Type(gettype($this->content()->defaultValue()->value()));
	}

	public function defaultValue(): ?string
	{
		$defaultValue = $this->content()->defaultValue()->value();
		if (is_array($defaultValue)) {
			return var_export($defaultValue, true);
		}
		return var_export($defaultValue, true);
	}

	public function returnTypes(): ?Types
	{
		if (!$this->reflection()->hasReturnType()) {
			return new Types(['void']);
		}
		return new Types([$this->reflection()->getReturnType()]);
	}
}
