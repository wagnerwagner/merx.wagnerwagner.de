<?php

use Kirby\Content\Field;
use Wagnerwagner\Site\ApiExamples;
use Wagnerwagner\Site\Type;

class ReferenceApiModelPage extends \Wagnerwagner\Site\ReferencePageAbstract
{
	/**
	 * The model rendered from the fixture shop, for each of its views
	 *
	 * @return array<string, array{request: string|null, data: array}>
	 */
	public function exampleResponses(): array
	{
		return ApiExamples::model((string)$this->class(), array_keys($this->views()->yaml()));
	}

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
