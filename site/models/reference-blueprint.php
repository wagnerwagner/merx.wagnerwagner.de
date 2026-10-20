<?php

use Kirby\Filesystem\F;

class ReferenceBlueprintPage extends \Wagnerwagner\Site\ReferencePageAbstract
{
	public function blueprintFileContent(): string
	{
		$relativeFilePath = $this->relativeFilePath();
		$root = $this->kirby()->plugin('wagnerwagner/merx')->root();
		return F::read($root . '/' . $relativeFilePath);
	}

	public function reflection(): Reflector|null
	{
		return null;
	}

	public function relativeFilePath(): string
	{
		return 'blueprints/' . $this->parent()->slug() . '/' . $this->filename();
	}
}
