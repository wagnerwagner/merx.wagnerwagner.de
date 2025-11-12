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

	public function call(): string
	{
		$pattern = $this->pattern();
		$method = $this->method();

		$call = "fetch('/api/$pattern', {\n";
		$call .= "	method: '$method',\n";
		$call .= "});";

		return $call;
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
			return new Types(['void']);
		}
		return new Types([$this->reflection()->getReturnType()]);
	}

	public function exceptions(): array
	{
		return [];
	}

	/**
	 * Returns the plugin-relative file path with the reflection start line
	 * appended as an anchor (e.g. `src/ListItems.php#L32`).
	 */
	public function filePath(): ?string
	{
		$root = $this->kirby()->plugin('ww/merx')->root() . '/';
		$filePath = Str::replace($this->reflection()->getFileName(), $root, '');
		$line = $this->reflection()->getStartLine();
		return $filePath . '#L' . $line;
	}

	/**
	 * Returns the versioned GitHub URL that points to the file and line of the
	 * reflected route action.
	 */
	public function gitHubUrl(): ?string
	{
		$gitHubRoot = option('github-repositories.merx');
		$version = $this->kirby()->plugin('ww/merx')->version();
		return $gitHubRoot . '/blob/' . $version . '/' . $this->filePath();
	}
}
