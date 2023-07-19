<?php
$link = $item->link()->isNotEmpty() ? $item->link()->toString() : null;
?>
<section id="<?= $item->uid() ?>">
  <div>
    <header>
      <h2>
        <?php if ($link): ?>
          <a href="<?= $item->link() ?>" rel="noopener">
        <?php endif ?>
        <?= $item->title() ?>
        <?php if ($link): ?>
          </a>
        <?php endif ?>
      </h2>
      <?php if ($item->byName()->isNotEmpty()): ?>
        <small>
          By
          <?php if ($item->byUrl()->isNotEmpty()): ?>
            <a href="<?= $item->byUrl() ?>" rel="noopener">
              <?= $item->byName() ?>
            </a>
          <?php else: ?>
            <?= $item->byName() ?>
          <?php endif; ?>
        </small>
      <?php endif; ?>
    </header>
    <?php if ($item->special()->isNotEmpty()): ?>
      <h3>What’s special</h3>
      <?= $item->special()->kt() ?>
    <?php endif; ?>
    <?php if ($link === null): ?>
      <em>Website is no longer available online.</em>
    <?php endif ?>
  </div>
  <?php if ($screenshot = $item->screenshot()->toFile()): ?>
    <?php if ($link): ?>
      <a href="<?= $item->link() ?>" rel="noopener">
    <?php else: ?>
      <div class="-figure">
    <?php endif ?>
      <figure>
        <?php if ($link): ?>
          <figcaption>
            <?= Url::short(Url::stripPath($item->link()->toString())) ?>
          </figcaption>
        <?php endif ?>

        <img
          src="<?= $screenshot->thumb([
            'width' => 768,
            'height' => 768 * 0.7 * 1.5,
            'crop' => 'top',
          ])->url() ?>"
          srcset="<?= $screenshot->thumb([
            'width' => 768 * 2,
            'height' => (768 * 0.7 * 1.5) * 2,
            'crop' => 'top',
          ])->url() ?> <?= 768 * 2 ?>w"
          sizes="
            (min-width: 50em) 36em,
            calc(100vw - 2rem)
          "
          width="768"
          height="<?= 768 * 0.7 * 1.5 ?>"
          loading="lazy"
          alt="Screenshot of “<?= $item->title() ?>”"
        >
      </figure>
    <?php if ($link): ?>
      </a>
    <?php else: ?>
      </div>
    <?php endif ?>
  <?php endif; ?>
</section>
