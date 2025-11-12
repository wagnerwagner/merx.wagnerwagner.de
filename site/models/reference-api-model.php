<?php

use Kirby\Cms\Page;
use Kirby\Content\Field;
use Kirby\Toolkit\Str;
use Wagnerwagner\Site\Type;

class ReferenceApiModelPage extends Page
{
	public function title(): Field
	{
		$class = $this->class();
		return parent::title()->value($class);
	}

	public function referenceClass(): Type
	{
		return new Type('Wagnerwagner\\Merx\\' . $this->class());
	}

	/**
	 * Returns the plugin-relative file path with the reflection start line
	 * appended as an anchor (e.g. `src/ListItems.php#L32`).
	 */
	public function filePath(): ?string
	{
		$filePath = 'api/models/' . $this->class() . '.php';
		return $filePath;
	}

	/**
	 * Returns the versioned GitHub URL that points to the file and line of the
	 * reflected model action.
	 * E.g. https://github.com/wagnerwagner/merx/blob/2.0.0-alpha.3/src/ListItems.php#L32
	 */
	public function gitHubUrl(): ?string
	{
		$gitHubRoot = option("github-repositories.merx");
		$version = $this->kirby()->plugin("ww/merx")->version();
		return $gitHubRoot . "/blob/" . $version . "/" . $this->filePath();
	}
}
