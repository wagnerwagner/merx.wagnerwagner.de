<?php

use Kirby\Filesystem\F;

class ReferenceBlueprintPage extends \Wagnerwagner\Site\ReferencePageAbstract
{
  public function reflection(): ?Reflector
  {
    return null;
  }

  public function relativeFilePath(): string
  {
    return 'blueprints/' . $this->parent()->slug() . '/' . $this->filename();
  }


  public function blueprintFileContent(): string
  {
    $relativeFilePath = $this->relativeFilePath();
    $root = $this->kirby()->plugin('ww/merx')->root();
    return F::read($root . '/' . $relativeFilePath);
  }
}
