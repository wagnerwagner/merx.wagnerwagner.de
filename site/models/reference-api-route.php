<?php

use Kirby\Cms\Page;
use Kirby\Content\Field;
use Kirby\Toolkit\Str;
use Wagnerwagner\Site\Types;

class ReferenceApiRoutePage extends Page
{
	public function title(): Field
	{
		$pattern = $this->pattern();
		$method = $this->method();
		return parent::title()->value($method . ' ' . $pattern);
	}

	public function reflection(): ReflectionFunction
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
			return null;
		}
		return new Types([$this->reflection()->getReturnType()]);
	}

	public function exceptions(): array
	{
		return [];
	}

	/**
	 * Path with line number
	 *
	 * @return ?string e.g. src/ListItems.php#L32
	 */
	public function filePath(): ?string
	{
		$root = $this->kirby()->plugin('ww/merx')->root() . '/';
		$filePath = Str::replace($this->reflection()->getFileName(), $root, '');
		$line = $this->reflection()->getStartLine();
		return $filePath . '#L' . $line;
	}

	/**
	 * @return string e.g. https://github.com/wagnerwagner/merx/blob/2.0.0-alpha.3/src/ListItems.php#L32
	 */
	public function gitHubUrl(): ?string
	{
		$gitHubRoot = option('github-repositories.merx');
		$version = $this->kirby()->plugin('ww/merx')->version();
		return $gitHubRoot . '/blob/' . $version . '/' . $this->filePath();
	}
}
