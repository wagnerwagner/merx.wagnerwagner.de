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
      <?php foreach($page->children()->unlisted()->shuffle() as $item): ?>
        <li>
          <?php if ($screenshot = $item->screenshot()->toFile()): ?>
            <a href="<?= $item->link() ?>" rel="noopener">
              <figure>
                <img
                  src="<?= $screenshot->thumb([
                    'width' => 336,
                    'height' => 336 * 0.7 * 1.5,
                    'crop' => 'top',
                  ])->url() ?>"
                  srcset="<?= $screenshot->thumb([
                    'width' => 336 * 2,
                    'height' => (336 * 0.7 * 1.5) * 2,
                    'crop' => 'top',
                  ])->url() ?> 2x"
                  width="336"
                  height="<?= 336 * 0.7 * 1.5 ?>"
                  loading="lazy"
                  alt="Screenshot of “<?= $item->title() ?>”"
                >
              </figure>
              <strong><?= $item->title() ?></strong>
              <small>
                <?= Url::short(Url::stripPath($item->link()->toString())) ?>
              </small>
            </a>
          <?php endif; ?>
        </li>
      <?php endforeach; ?>
    </ul>
    <footer>
      <?= $page->footerText()->kt() ?>
    </footer>
  </main>
  <?php snippet('footer') ?>
</body>
<?php snippet('foot') ?>
