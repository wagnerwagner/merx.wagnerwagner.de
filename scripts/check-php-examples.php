<?php

/**
 * Runs `php -l` on every PHP code block of the documentation
 *
 * php scripts/check-php-examples.php
 *
 * A block without a PHP tag is plain PHP and gets one. Every block has to
 * be valid on its own: close what you open, also in an excerpt.
 */

$root = dirname(__DIR__) . '/content';
$files = new RegexIterator(
	new RecursiveIteratorIterator(new RecursiveDirectoryIterator($root, FilesystemIterator::SKIP_DOTS)),
	'!\.txt$!',
);

$checked = 0;
$failed = [];

foreach ($files as $file) {
	$path = (string)$file;

	if (str_contains($path, '/_drafts/') === true) {
		continue;
	}

	preg_match_all('!^```php[^\n]*\n(.*?)^```!ms', file_get_contents($path), $blocks, PREG_SET_ORDER | PREG_OFFSET_CAPTURE);

	foreach ($blocks as $block) {
		$code = $block[1][0];

		if (str_contains($code, '<?') === false) {
			$code = '<?php' . PHP_EOL . $code;
		}

		$tmp = tempnam(sys_get_temp_dir(), 'merx-docs-');
		file_put_contents($tmp, $code);
		exec('php -l ' . escapeshellarg($tmp) . ' 2>&1', $output, $exitCode);
		unlink($tmp);
		$checked++;

		if ($exitCode !== 0) {
			$line = substr_count(file_get_contents($path), "\n", 0, $block[0][1]) + 1;
			$failed[] = substr($path, strlen($root) + 1) . ':' . $line . PHP_EOL
				. '  ' . preg_replace('! in /\S+!', '', trim(implode(' ', array_filter($output, fn ($l) => str_contains($l, 'error')))));
		}

		$output = [];
	}
}

echo implode(PHP_EOL, $failed) . (count($failed) > 0 ? PHP_EOL . PHP_EOL : '');
echo $checked . ' PHP examples checked, ' . count($failed) . ' failed.' . PHP_EOL;

exit(count($failed) > 0 ? 1 : 0);
