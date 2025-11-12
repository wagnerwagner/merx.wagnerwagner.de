<?php
/** @var \Kirby\Cms\Block $block */
?>
<section id="<?= $block->id() ?>" class="section" aria-label="<?= $block->title() ?>">
	<h2><?= $block->title() ?></h2>
	<div class="m-text" data-size="large">
		<?= $block->abstract()->kt() ?>
	</div>
	<div class="m-text">
		<?= $block->text()->kt() ?>
	</div>
</section>
