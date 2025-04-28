<?php

/** @var \Kirby\Cms\Page $page */
/** @var \Kirby\Cms\Blocks $sections */

if ($page->redirect()->isTrue()) {
  $page->children()->listed()->first()?->go();
}
?>
<?php snippet('head') ?>
<body>
  <?php snippet('header') ?>
  <div class="center">
    <?php snippet('subnav'); ?>
    <main class="doc" aria-label="<?= $page->title() ?>">
      <h1><?= $page->headline()->isNotEmpty() ? $page->headline() : $page->title() ?></h1>
      <div class="text">
        <?= $page->text()->kt() ?>
      </div>
      <?php if ($sections->count() > 0): ?>
        <?php snippet('toc'); ?>
        <?= $sections ?>
      <?php endif ?>
    </main>
  </div>
  <?php snippet('footer') ?>
</body>
<?php snippet('foot') ?>
