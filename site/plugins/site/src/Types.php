<?php

namespace Wagnerwagner\Site;

use Kirby\Cms\Html;
use Kirby\Toolkit\Collection;
use Kirby\Toolkit\Str;
use Reflector;

/**
 * @extends \Kirby\Toolkit\Collection<\Wagnerwagner\Site\Type>
 */
class Types extends Collection {
  public array $data = [];

  public function __construct(array|string $types, ?Reflector $reflector = null)
  {
		if (is_string($types)) {
			$types = Str::replace($types, '?', 'null|');
			$types = explode('|', $types);
		}
    $this->data = array_map(fn ($type) => new Type($type, $reflector), $types);
  }

  public function toHtml(bool $codeBlock = true, ?string $baseUrl = null, ?bool $api = null): string
  {
    if ($this->count() === 0) {
      return Html::tag('code', 'mixed');
    }

		$seperator = ' <span class="a-separator" aria-hidden="true">|</span><span class="a-visually-hidden">or</span> ';

    return implode($seperator, array_map(fn (Type $type) => $type->toHtml($codeBlock, $baseUrl, $api), $this->data));
  }

  public function __toString(): string
  {
    return implode('|', $this->data);
  }
}
