<?php
use Wagnerwagner\Site\MyReflectionMethod;

class SectionClassMethodPage extends Page {
  public function url($options = null): string
  {
    return $this->parent()->url() . '#' . $this->uid();
  }

  public function reflectionMethod(): ?MyReflectionMethod
  {
    return new MyReflectionMethod((string)$this->className(), (string)$this->methodName()) ?? null;
  }
}
