<?php snippet('head') ?>
<body>
  <?php snippet('header') ?>
  <main class="home" aria-label="<?= $page->title() ?>">
    <div class="hero">
      <div class="hero__copy">
        <h1><?= $page->heroCopy() ?></h1>
      </div>
      <div class="hero__buttons">
        <div>
          <a class="button" href="https://www.babyreport.de">Example Shop&ensp;→</a>
          <a class="link-secondary" href="https://github.com/wagnerwagner/babyreport.de">Source Code</a>
        </div>
        <div>
          <a class="button" href="https://github.com/wagnerwagner/merx/releases/latest">Download&ensp;↓</a>
          <div class="text-secondary">
            <a class="link-secondary" href="https://github.com/wagnerwagner/merx">Source Code</a>
          </div>
        </div>
      </div>
    </div>
    <div class="features">
      <?php foreach($page->features()->toStructure() as $item): ?>
        <div class="features__item">
          <h2><?= $item->headline() ?></h2>
          <?= $item->text()->kt() ?>
        </div>
      <?php endforeach;?>
    </div>
  </main>
  <?php snippet('footer') ?>
</body>
<?php snippet('foot') ?>
