<?php

namespace Wagnerwagner\Site;

use Kirby\Cms\Html;
use Kirby\Toolkit\Str;
use Reflector;

class Type
{
	public string|null $alias = null;

	/** string, class, float, int, etc. */
	public string|null $dataType = null;
	public string|null $type = null;

	public function __construct(string $type, Reflector|null $reflector = null)
	{
		if (
			in_array($type, ['self', 'static', '$this']) === true ||
			Str::startsWith($type, '$')
		) {
			$this->type = $reflector !== null && method_exists($reflector, 'getDeclaringClass') ? $reflector?->getDeclaringClass()?->getName() ?? $type : $type;
			$this->alias = $type;
			$this->dataType = 'class';
		} elseif (
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

	public function __toString(): string
	{
		return $this->type === 'mixed' ? '' : $this->type;
	}

	public function getDataType(Reflector|null $reflector = null): string|null
	{
		$type = Str::lower($this->type);
		$type = in_array($type, ['boolean', 'true', 'false']) ? 'bool' : $type;
		if (in_array($type, ['string', 'null', 'bool', 'int', 'float', 'array'])) {
			return $type;
		}
		if ($reflector instanceof Reflector || Str::contains($type, '\\') || ctype_upper(Str::substr($this->type, 0, 1))) {
			return 'class';
		}
		if (in_array($type, ['function'])) {
			return 'callable';
		}
		if (is_string($type)) {
			if (Str::startsWith($type, "'") && Str::endsWith($type, "'")) {
				return 'string';
			}
			if (Str::endsWith($type, '()')) {
				return 'callable';
			}
		}
		return null;
	}

	public function toHtml(bool $codeBlock = true, string|null $baseUrl = null, bool|null $api = null, $short = false): string
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
			} elseif (Str::startsWith($type, 'Wagnerwagner\\Merx\\')) {
				$baseUrl = $api === true ? url('/reference/api/models/') : url('/reference/classes/');
			}
		}
		if (Str::startsWith($type, 'Kirby\\')) {
			$slug = Str::replace($slug, 'kirby/', '');
		} elseif (Str::startsWith($type, 'Wagnerwagner\\Merx\\')) {
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
				...['href' => $link, ], $attr,
			]);
		}

		return $html;
	}

	public function url(): string|null
	{
		return null;
	}
}
