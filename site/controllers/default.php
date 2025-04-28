<?php

return function ($page) {
  return [
    'sections' => $page->sections()->toBlocks(),
  ];
};
