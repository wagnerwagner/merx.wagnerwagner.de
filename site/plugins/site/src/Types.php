<?php

namespace Wagnerwagner\Site;

use Kirby\Cms\Html;
use Kirby\Toolkit\Iterator;

/**
 * @extends \Kirby\Toolkit\Iterator<\Wagnerwagner\Site\Type>
 */
class Types extends Iterator {
  public array $data = [];

  public function __construct(array $types, ?\ReflectionMethod $reflector = null)
  {
    $this->data = array_map(fn ($type) => new Type($type, $reflector), $types);
  }

  public function toHtml(bool $codeBlock = true): string
  {
    if ($this->count() === 0) {
      return Html::tag('code', 'mixed');
    }

    return implode('|', array_map(fn (Type $type) => $type->toHtml($codeBlock), $this->data));
  }

  public function __toString(): string
  {
    return implode('|', $this->data);
  }
}
