<?php
/** @var \Kirby\Cms\Block $block */

/** @var \Kirby\Reference\Reflectable\ReflectableClassMethod $reflection */
$reflection = $block->content()->data()['reflection'];
?>
<section id="<?= $block->id() ?>" class="section" aria-label="<?= $block->title() ?>">
	<h2><?= $block->title() ?></h2>
	<div class="m-text" data-size="large">
		<?= $reflection->summary() ?>
	</div>
	<div class="m-text">
		<?= $reflection->description() ?>
	</div>
</section>
