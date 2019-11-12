<?php

class ProductPage extends Page
{
  public function getNet(): float
  {
    return calculateNet($this->price()->toFloat(), $this->tax()->toFloat());
  }
}
