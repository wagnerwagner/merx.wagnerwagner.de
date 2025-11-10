<?php

namespace Wagnerwagner\Site;

use Kirby\Cms\Html;
use Kirby\Toolkit\Str;

class Type {
  public ?string $type = null;

  public ?string $alias = null;

  public function __construct(string $type, ?\ReflectionMethod $reflector = null)
  {
    if (in_array($type, ['self', 'static', '$this']) === true) {
      $this->alias = $type;
      $this->type = $reflector?->getDeclaringClass()->getName() ?? $type;
    } else {
      $this->type = $type;
    }
  }

  public function toHtml(bool $codeBlock = true): string
  {
    $type = $this->type;
    $link = null;
    $slugs = array_map(fn ($item) => Str::kebab($item), Str::split($type, '\\'));
    $slug = implode('/', $slugs);
    if (Str::startsWith($type, 'Kirby\\')) {
      $slug = Str::replace($slug, 'kirby/', '');
      $link = 'https://getkirby.com/docs/reference/objects/' . $slug;
    } else if (Str::startsWith($type, 'Wagnerwagner\\Merx\\')) {
      $slug = Str::replace($slug, 'wagnerwagner/merx/', '');
      $link = url('/reference/classes/' . $slug);
    }
    $html = '';
    if ($codeBlock) {
      $html = Html::tag('code', $type);
    }
    if (is_string($link)) {
      $html = Html::tag('a', [$html], [
        'href' => $link,
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
