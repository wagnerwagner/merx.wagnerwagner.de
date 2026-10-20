<?php

use Kirby\Content\Field;
use Wagnerwagner\Site\Type;
use Wagnerwagner\Site\Types;

class ReferenceApiRoutePage extends \Wagnerwagner\Site\ReferencePageAbstract
{
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
		$call .= '});';

		return $call;
	}

	public function exceptions(): array
	{
		return [];
	}

	public function params(): array
	{
		return $this->reflection()->getParameters();
	}

	public function reflection(): Reflector|null
	{
		return $this->content()->reflection()->value();
	}

	public function returnType(): Type|null
	{
		return $this->returnTypes()->first();
	}

	public function returnTypes(): Types|null
	{
		if (!$this->reflection()->hasReturnType()) {
			return new Types(['void']);
		}
		return new Types([$this->reflection()->getReturnType()]);
	}

	public function title(): Field
	{
		$pattern = $this->pattern();
		$method = $this->method();
		return parent::title()->value($method . ' ' . $pattern);
	}
}
