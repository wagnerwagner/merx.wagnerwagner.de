<?php

/** @var string $name */
/** @var ?int $weight */

$name ??= null;
$weight ??= 400;

if ($name === null) return null;

$filename = $name . '-' . $weight . '.svg';
?>
<span class="a-icon"><?= svg('/assets/images/icons/' . $filename) ?></span>
