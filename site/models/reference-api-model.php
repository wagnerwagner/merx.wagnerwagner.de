<?php

use Kirby\Content\Field;
use Wagnerwagner\Site\Type;

class ReferenceApiModelPage extends \Wagnerwagner\Site\ReferencePageAbstract
{
	public function referenceClass(): Type
	{
		return new Type('Wagnerwagner\\Merx\\' . $this->class());
	}

	public function reflection(): Reflector|null
	{
		return null;
	}

	/**
	 * Returns the plugin-relative file path with the reflection start line
	 * appended as an anchor (e.g. `src/ListItems.php#L32`).
	 */
	public function relativeFilePath(): string|null
	{
		$relativeFilePath = 'api/models/' . $this->class() . '.php';
		return $relativeFilePath;
	}

	public function title(): Field
	{
		$class = $this->class();
		return parent::title()->value($class);
	}
}
