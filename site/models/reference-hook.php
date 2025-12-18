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
}

