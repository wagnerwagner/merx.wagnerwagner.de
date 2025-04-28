<?php
/** @var \Kirby\Cms\Block $block */

/** @var \Kirby\Reference\Reflectable\ReflectableClassMethod $reflection */
$reflection = $block->content()->data()['reflection'];
?>
<section id="<?= $block->id() ?>" class="section" aria-label="<?= $block->title() ?>">
  <h2><?= $block->title() ?></h2>
  <div class="text text--big">
    <?= $reflection->summary() ?>
  </div>
  <div class="text">
    <?= $reflection->description() ?>
  </div>
</section>
