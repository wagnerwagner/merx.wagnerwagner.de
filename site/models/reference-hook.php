<?php

use Kirby\Content\Field;
use Kirby\Toolkit\Str;
use Wagnerwagner\Site\ReferencePageAbstract;
use Wagnerwagner\Site\Types;

class ReferenceHookPage extends ReferencePageAbstract
{
	public function title(): Field
	{
		$key = $this->key()->value();
		return parent::title()->value(Str::replace($key, 'wagnerwagner.merx.', ''));
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

		return "'" . $this->key() . "' => function (" . implode(', ', $parameters) . "): " . $this->returnTypes() . ' {}';
	}
}

