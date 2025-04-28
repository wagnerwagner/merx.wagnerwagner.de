<?php

use Kirby\Cms\Blocks;

return function (ReferenceClassPage $page) {
  $blocks = [];

  $children = $page->children();

  foreach ($children as $child) {
    $blocks[] = [
      'content' => [
        'title' => $child->title(),
        'reflection' => $child->reflection(),
      ],
      'type' => 'reflection-method',
      'id' => $child->slug(),
    ];
  }

  return [
    'sections' => Blocks::factory($blocks),
  ];
};
