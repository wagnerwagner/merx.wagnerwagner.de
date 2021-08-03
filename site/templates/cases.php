<?php
if ($page->redirect()->isTrue()) {
  go($page->children()->listed()->first()->url());
}
?>
<?php snippet('head') ?>
<body class="l-cases">
  <?php snippet('header') ?>
  <main class="cases">
    <h1><?= $page->headline() ?></h1>
    <?php foreach($page->children()->listed() as $item): ?>
      <?php snippet('case', compact('item')); ?>
    <?php endforeach; ?>
    <ul>
      <?php foreach($page->children()->unlisted()->shuffle()->limit(6) as $item): ?>
        <?php snippet('case-list-item', compact('item')) ?>
      <?php endforeach; ?>
    </ul>
    <footer>
      <?= $page->footerText()->kt() ?>
    </footer>
  </main>
  <?php snippet('footer') ?>
</body>
<?php snippet('foot') ?>
