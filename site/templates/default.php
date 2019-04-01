<?php
if ($page->redirect()->isTrue()) {
  go($page->children()->listed()->first()->url());
}
?>
<?php snippet('head') ?>
<body>
  <?php snippet('header') ?>
  <div class="center">
    <?php snippet('subnav'); ?>
    <main class="doc" aria-label="<?= $page->title() ?>">
      <h1><?= $page->title() ?></h1>
      <div class="text">
        <?= $page->text()->kt() ?>
      </div>
      <?php if ($page->sections()->count() > 0): ?>
      <?php snippet('toc'); ?>
      <?php foreach($page->sections() as $section): ?>
        <?php snippet($section->intendedTemplate(), ['section' => $section]); ?>
      <?php endforeach; ?>
      <?php endif; ?>
    </main>
  </div>
  <?php snippet('footer') ?>
</body>
<?php snippet('foot') ?>
