<?php

namespace Wagnerwagner\Site;

use Kirby\Cms\Html;
use Kirby\Toolkit\Str;
use Reflector;

class Type {
	public ?string $type = null;

	public ?string $alias = null;

	/** string, class, float, int, etc. */
	public ?string $dataType = null;

	public function __construct(string $type, ?Reflector $reflector = null)
	{
		if (
			in_array($type, ['self', 'static', '$this']) === true
			|| Str::startsWith($type, '$')
		) {
			$this->type = $reflector !== null && method_exists($reflector, 'getDeclaringClass') ? $reflector?->getDeclaringClass()?->getName() ?? $type : $type;
			$this->alias = $type;
			$this->dataType = 'class';
		} else if (
			Str::startsWith($type, 'Wagnerwagner\\')
		) {
			$this->type = $type;
			$this->alias = $type;
			$this->dataType = 'class';
		} else {
			$type = $type === 'NULL' ? 'null' : $type;
			$this->type = $type;
			$this->dataType = $this->getDataType($reflector);
		}
	}

	public function getDataType(?Reflector $reflector = null): ?string
	{
		$type = Str::lower($this->type);
		$type = $type === 'boolean' ? 'bool' : $type;
		if (in_array($type, ['string', 'null', 'bool', 'int', 'float', 'array'])) {
			return $type;
		} else if ($reflector instanceof Reflector || Str::contains($type, '\\')) {
			return 'class';
		}
		return null;
	}

	public function toHtml(bool $codeBlock = true, ?string $baseUrl = null, ?bool $api = null, $short = false): string
	{
		$type = $this->type;
		if (Str::startsWith($type, '\\')) {
			$type = Str::substr($type, 1);
		}
		$link = null;
		$parts = array_map(fn ($item) => Str::kebab($item), Str::split($type, '\\'));
		$slug = implode('/', $parts);
		if ($baseUrl === null) {
			if (Str::startsWith($type, 'Kirby\\')) {
				$baseUrl = 'https://getkirby.com/docs/reference/objects/';
			} else if (Str::startsWith($type, 'Wagnerwagner\\Merx\\')) {
				$baseUrl = $api === true ? url('/reference/api/models/') : url('/reference/classes/');
			}
		}
		if (Str::startsWith($type, 'Kirby\\')) {
			$slug = Str::replace($slug, 'kirby/', '');
		} else if (Str::startsWith($type, 'Wagnerwagner\\Merx\\')) {
			$slug = Str::replace($slug, 'wagnerwagner/merx/', '');
		}
		if ($baseUrl !== null) {
			$link = $baseUrl . Str::replace($slug, '[]', '');
		}
		$html = '';
		$attr = [
			'class' => 'a-type',
			'data-type' => $this->dataType,
		];
		if ($codeBlock) {
			$html = Html::tag('code', $type, $attr);
		}

		// remove Wagnerwagner\Merx\
		if ($short === true) {
			$type = Str::replace($type, 'Wagnerwagner\\Merx\\', '');
		}

		if (is_string($link)) {
			$html = Html::tag('a', [Html::tag('code', $type)], [
				...['href' => $link,], $attr,
			]);
		}

		return $html;
}

	public function url(): ?string
	{
		return null;
	}

	public function __toString(): string
	{
		return $this->type === 'mixed' ? '' : $this->type;
	}
}
