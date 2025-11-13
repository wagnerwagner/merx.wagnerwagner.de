<?php

use Kirby\Content\Field;
use Kirby\Toolkit\Str;
use Wagnerwagner\Site\Types;

class ReferenceApiRoutePage extends \Wagnerwagner\Site\ReferencePageAbstract
{
	public function title(): Field
	{
		$pattern = $this->pattern();
		$method = $this->method();
		return parent::title()->value($method . ' ' . $pattern);
	}

  public function apiModelBaseUrl(): string
  {
    return url($this->parent()->parent()->id() . '/models/');
  }

	public function call(): string
  {
		$pattern = $this->pattern();
		$method = $this->method();

		$call = "fetch('/api/$pattern', {\n";
		$call .= "	method: '$method',\n";
		$call .= "});";

		return $call;
  }

	public function reflection(): ?Reflector
	{
		return $this->content()->reflection()->value();
	}

	public function params(): array
	{
		return $this->reflection()->getParameters();
	}

	public function returnTypes(): ?Types
	{
		if (!$this->reflection()->hasReturnType()) {
			return new Types(['void']);
		}
		return new Types([$this->reflection()->getReturnType()]);
	}

  public function returnType(): ?Type
  {
    return $this->returnTypes()[0]->type;
  }

	public function exceptions(): array
	{
		return [];
	}
}
