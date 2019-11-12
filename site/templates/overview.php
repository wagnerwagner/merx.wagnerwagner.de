<?php snippet('head') ?>
<body class="is-overview">
  <?php snippet('header') ?>
  <main class="overview" aria-label="<?= $page->title() ?>">
    <div class="text">
      <?= $page->text()->kt() ?>
    </div>
    <nav class="overview__nav">
      <?php foreach($page->children()->listed() as $item): ?>
        <?php if ($item->uid() === 'options'): ?>
          <hr>
        <?php endif; ?>
        <?php if ($item->hasListedChildren()): ?>
          <h2><?= $item->title() ?></h2>
          <?php foreach($item->children()->listed() as $item): ?>
            <a <?= $item->shortDescription()->isEmpty() ? 'class="is-small"' : '' ?> href="<?= $item->url() ?>">
              <strong><?= $item->title() ?></strong>
              <?php if ($item->shortDescription()->isNotEmpty()): ?>
                <p><?= $item->shortDescription() ?></p>
              <?php endif; ?>
            </a>
          <?php endforeach; ?>
        <?php else: ?>
          <a <?= $item->shortDescription()->isEmpty() ? 'class="is-small"' : '' ?> href="<?= $item->url() ?>">
            <strong><?= $item->title() ?></strong>
            <?php if ($item->shortDescription()->isNotEmpty()): ?>
              <p><?= $item->shortDescription() ?></p>
            <?php endif; ?>
          </a>
        <?php endif; ?>
      <?php endforeach; ?>
    </nav>
  </main>
  <?php snippet('footer') ?>
</body>
<?php snippet('foot') ?>
