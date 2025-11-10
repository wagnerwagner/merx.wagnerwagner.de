<?php

/** @var ReferenceClassPage $page */
/** @var \Kirby\Cms\Blocks $sections */

var_dump($page->reflection());
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
      <nav class="toc">
        <div>
          <h4>Table of Contents</h4>
          <ul>
            <?php foreach($page->children()->listed() as $classMethod): ?>
              <li>
                <a href="<?= $classMethod->url() ?>"><?= $classMethod->title() ?></a>
              </li>
            <?php endforeach; ?>
          </ul>
        </div>
      </nav>

    </main>
  </div>
  <?php snippet('footer') ?>
</body>
<?php snippet('foot') ?>
