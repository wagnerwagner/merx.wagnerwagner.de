<?php

return function () {
  return [
    'results' => site()->prettySearch(get('q')),
  ];
};
