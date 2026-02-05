<?php

use Kirby\Cms\App;

include '../kirby/bootstrap.php';

$kirby = new App([
		'roots' => [
				'index' => __DIR__,
				'content' => __DIR__ . '/../content',
				'site' => __DIR__ . '/../site',
		],
]);
echo $kirby->render();
