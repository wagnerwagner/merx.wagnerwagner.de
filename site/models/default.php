<?php
use Kirby\Cms\Page;
use Kirby\Http\Url;

class DefaultPage extends Page {
  public function listedChildren(): Kirby\Cms\Pages
  {
    return parent::children()->listed()->filter(function($child) {
      return !Str::startsWith($child->intendedTemplate()->name(), 'section');
    });
  }

  public function sections(): Kirby\Cms\Pages
  {
    return parent::children()->listed()->filter(function($child) {
      return Str::startsWith($child->intendedTemplate()->name(), 'section');
    });
  }

  public function hasListedChildren(): bool
  {
      return $this->listedChildren()->count() > 0;
  }
}
