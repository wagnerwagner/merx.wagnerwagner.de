<?php

class CasePage extends Page {
  public function url($options = null): string
  {
    return $this->parent()->url() . '#' . $this->uid();
  }
}
