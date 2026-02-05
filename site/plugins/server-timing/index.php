<?php

use Kirby\Cms\App;
use Kirby\Exception\Exception;
use Kirby\Toolkit\Config;
use Kirby\Toolkit\Str;

function serverTimingStart($name): float
{
	$name = Str::slug($name);
	$startTime = microtime(true);
	Config::set('tobiaswolf.server-timing.data.start.' . $name, $startTime);
	return $startTime;
}
function serverTimingEnd($name): float
{
	$name = Str::slug($name);
	$sum = Config::get('tobiaswolf.server-timing.data.sum.' . $name, 0);
	$start = Config::get('tobiaswolf.server-timing.data.start.' . $name);
	if ($start) {
		$sum += (microtime(true) - $start);
		$sumData = Config::get('tobiaswolf.server-timing.data.sum', []);
		Config::set('tobiaswolf.server-timing.data.sum', array_merge($sumData, [
			$name => $sum,
		]));
	} elseif (option('debug') && false) {
		throw new Exception([
			'fallback' => 'Error in server-timing plugin. No start time for ' . $name,
		]);
	}
	return $sum;
}

App::plugin('tobiaswolf/server-timing', [
	'hooks' => [
		'route:before' => function () {
			serverTimingStart('route');
		},
		'route:after' => function () {
			serverTimingEnd('route');
		},
		'page.render:before' => function() {
			serverTimingStart('page.render');
		},
		'page.render:after' => function() {
			serverTimingEnd('page.render');

			$sums = Config::get('tobiaswolf.server-timing.data.sum') ?? [];
			$strings = [];
			foreach ($sums as $name => $dur) {
				$strings[] = $name . ';dur=' . $dur * 1000;
			}

			header('Server-Timing: ' . implode(', ', $strings));
		},
	]
]);
