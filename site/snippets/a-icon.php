<?php

/** @var string $name */
/** @var ?int $weight */

$name ??= null;
$weight ??= 400;

if ($name === null) return null;

$iconName = $name . '-' . $weight;
?>
<svg class="a-icon" aria-hidden="true" focusable="false"><use href="<?= url('assets/images/icons.svg') ?>#<?= $iconName ?>"></use></svg>
