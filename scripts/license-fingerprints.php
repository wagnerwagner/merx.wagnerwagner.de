<?php

/**
 * Writes the fingerprints of the license keys into the Merx plugin
 *
 *   composer license-keys             the first 10000 keys
 *   composer license-keys -- 20000    more of them, before they run out
 *
 * Merx accepts the keys on this list and no other, and a shop learns of new
 * ones with a Merx release. Commit the file in `site/plugins/merx`.
 *
 * Needs `wagnerwagner.site.licenseSecret` in `site/config/env.php`, the same
 * one as on the server. With another secret every key sold so far would turn
 * invalid, which is why a list which drops a fingerprint is not written.
 */

use Kirby\Cms\App;
use Kirby\Filesystem\F;
use Wagnerwagner\Site\Licenses;

require dirname(__DIR__) . '/kirby/bootstrap.php';

new App([
	'roots' => [
		'index' => dirname(__DIR__) . '/public',
		'content' => dirname(__DIR__) . '/content',
		'site' => dirname(__DIR__) . '/site',
	],
]);

$count = (int)($argv[1] ?? 10000);
$file = Licenses::file();

try {
	$fingerprints = Licenses::fingerprints($count);
} catch (Throwable $ex) {
	fwrite(STDERR, $ex->getMessage() . "\n");
	exit(1);
}

$dropped = array_diff(str_split((string)F::read($file), 10), str_split($fingerprints, 10));

if (count($dropped) > 0) {
	fwrite(STDERR, count($dropped) . " fingerprints of the current list would be dropped. Is the secret another one, or the number lower?\n");
	exit(1);
}

F::write($file, $fingerprints);

echo 'Wrote the fingerprints of ' . $count . ' license keys to ' . $file . "\n";
