<?php
/** @var \Kirby\Cms\Block $block */
?>
<section id="<?= $block->id() ?>" class="section" aria-label="<?= $block->title() ?>">
  <h2><?= $block->title() ?></h2>
  <div class="text text--big">
    <?= $block->abstract()->kt() ?>
  </div>
  <div class="text">
    <?= $block->text()->kt() ?>
  </div>
</section>
