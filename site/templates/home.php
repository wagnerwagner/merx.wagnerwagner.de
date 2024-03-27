
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
              <a class="button" href="https://starterkit.merx.wagnerwagner.de" rel="noopener">Starterkit&ensp;→</a>
              <a class="link-secondary" href="https://github.com/wagnerwagner/merx-starterkit" rel="noopener">Source Code</a>
            </div>
            <div>
              <a class="button" href="https://github.com/wagnerwagner/merx/releases/latest" rel="noopener">Download&ensp;↓</a>
              <div class="text-secondary">
                <a class="link-secondary" href="https://github.com/wagnerwagner/merx" rel="noopener">Source Code</a>
              </div>
            </div>
          </div>
        </div>
        <?php if ($heroImage = $page->heroImage()->toFile()): ?>
        <div class="hero__image">
          <a href="https://starterkit.merx.wagnerwagner.de" rel="noopener">
            <img src="<?= $heroImage->crop(704, 704 * 0.58, 'top')->url() ?>" srcset="<?= $heroImage->crop(704 * 2, 704 * 2 * 0.58, 'top')->url() ?> 2x" alt="<?= $heroImage->alt() ?>">
          </a>
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
    <?php $casesPage = page('showcase'); ?>
    <?php if ($casesPage): ?>
      <aside class="home-showcase">
        <h2>
          <a href="<?= $casesPage->url() ?>">
            <?= $casesPage->headline() ?>
          </a>
        </h2>
        <ul>
          <?php foreach ($casesPage->children()->listed()->shuffle()->limit(4) as $item): ?>
            <?php snippet('case-list-item', compact('item')) ?>
          <?php endforeach; ?>
        </ul>
        <div>
          <a href="<?= $casesPage->url() ?>">Show more examples →</a>
        </div>
      </aside>
    <?php endif; ?>
  </main>
  <?php snippet('footer') ?>
</body>
<?php snippet('foot') ?>
