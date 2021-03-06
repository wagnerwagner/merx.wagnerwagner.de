<?php snippet('head') ?>
<body>
  <?php snippet('header') ?>
  <main class="home" aria-label="<?= $page->title() ?>">
    <div class="hero">
      <div class="hero__container">
        <div class="hero__copy">
          <h1><?= $page->heroCopy() ?></h1>
          <div class="hero__buttons">
            <div>
              <a class="button" href="https://www.babyreport.de">Example Shop&ensp;→</a>
              <a class="link-secondary" href="https://github.com/wagnerwagner/babyreport.de">Source Code</a>
            </div>
            <div>
              <a class="button" href="http://plainkit-merx.wagnerwagner.de">Plainkit&ensp;→</a>
              <a class="link-secondary" href="https://github.com/wagnerwagner/merx-examples">Source Code</a>
            </div>
            <div>
              <a class="button" href="https://github.com/wagnerwagner/merx/releases/latest">Download&ensp;↓</a>
              <div class="text-secondary">
                <a class="link-secondary" href="https://github.com/wagnerwagner/merx">Source Code</a>
              </div>
            </div>
          </div>
        </div>
        <?php if ($heroImage = $page->heroImage()->toFile()): ?>
        <div class="hero__image">
          <img src="<?= $heroImage->crop(704, 704 * 0.58, 'top')->url() ?>" srcset="<?= $heroImage->crop(704 * 2, 704 * 2 * 0.58, 'top')->url() ?> 2x" alt="<?= $heroImage->alt() ?>">
        </div>
        <?php endif; ?>
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
