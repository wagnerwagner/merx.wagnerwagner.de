
<?php
/**
 * Note block inside of text
 *
 * @var string $html
 * @var string $caption filepath
 */
$caption = $caption ?? null;
?>
<figure class="m-code">
	<?php if ($caption): ?>
		<figcaption><?= $caption ?></figcaption>
	<?php endif ?>

	<?= $html ?>
</figure>
