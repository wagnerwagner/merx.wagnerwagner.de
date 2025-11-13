<?php

use Kirby\Content\Field;
use Kirby\Toolkit\A;
use Kirby\Toolkit\Str;
use Wagnerwagner\Site\ReferencePageAbstract;
use Wagnerwagner\Site\Type;
use Wagnerwagner\Site\Types;

class ReferenceHookPage extends ReferencePageAbstract
{
	public function title(): Field
	{
		$key = $this->key()->value();
		return parent::title()->value($key);
	}

	public function key(): Field
	{
		return $this->content()->get('key');
	}

  public function returnTypes(): ?Types
  {
    if (!$this->reflection()->hasReturnType()) {
      return null;
    }
    return new Types([$this->reflection()->getReturnType()]);
  }

	public function reflection(): ?Reflector
	{
		return $this->content()->hook()->value();
	}

  public function docBlock(): ?PhpDocNode
	{
		$reflection = $this->reflection();
		if ($reflection->getDocComment() === false) {
			return null;
		}
		$docblock  = $reflection->getDocComment();
		$config    = new ParserConfig(usedAttributes: []);
		$lexer     = new Lexer($config);
		$constExpr = new ConstExprParser($config);
		$type      = new TypeParser($config, $constExpr);
		$phpDoc    = new PhpDocParser($config, $type, $constExpr);
		$tokens    = new TokenIterator($lexer->tokenize($docblock));
		$node      = $phpDoc->parse($tokens);
		return $node;
	}

  public function params(): array
  {
    $reflection = $this->reflection();

		return A::map($reflection->getParameters(), function (ReflectionParameter $param) use ($reflection) {
			/** @var ?\PHPStan\PhpDocParser\Ast\PhpDoc\ParamTagValueNode */
			$docBlockParam = A::find(
				$this->docBlock()?->getParamTagValues() ?? [],
				fn (ParamTagValueNode $docBlockParam) => $docBlockParam->parameterName === '$' . $param->name);

			$defaultValue = null;
			if ($param->isDefaultValueAvailable()) {
				$defaultValue = $param->getDefaultValue();
				if (gettype($defaultValue) === 'array') {
					$defaultValue = '[]';
				} else if (gettype($defaultValue) === 'boolean') {
					$defaultValue = $defaultValue ? 'true' : 'false';
				} else if (gettype($defaultValue) === 'NULL') {
					$defaultValue = 'null';
				} else if (gettype($defaultValue) === 'string') {
					$defaultValue = "'$defaultValue'";
				}
			};
			$name = '$' . $param->getName();
			$types = $docBlockParam->type->types ?? null;
			if ($types === null && $param->getType() !== null) {
				$types = explode('|', Str::replace($param->getType() ?? '', '?', 'null|'));
			}
			$types = new Types($types ?? [], $reflection);
			$name = $param->isVariadic() ? '...' . $name : $name;
			return [
				'name' => $name,
				'types' => $types,
				'defaultValue' => $defaultValue,
				'description' => $docBlockParam?->description,
			];
		});
  }
}

