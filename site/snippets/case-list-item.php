<?php
$link = $item->link()->isNotEmpty() ? $item->link()->toString() : null;
$width = 336;
$height = $width * 0.7 * 1.5;
if ($page->isHomePage()) {
  $link = $item->url();
  $width = 352;
  $height = $width * 0.75;
}
?>
<li class="case-list-item">
  <?php if ($screenshot = $item->screenshot()->toFile()): ?>
    <?php if ($link): ?>
      <a href="<?= $link ?>" rel="noopener">
    <?php else: ?>
      <div>
    <?php endif ?>
      <figure>
        <img
          src="<?= $screenshot->thumb([
            'width' => $width,
            'height' => $height,
            'crop' => 'top',
          ])->url() ?>"
          srcset="<?= $screenshot->thumb([
            'width' => $width * 2,
            'height' => $height * 2,
            'crop' => 'top',
          ])->url() ?> 2x"
          width="<?= $width ?>"
          height="<?= $height ?>"
          loading="lazy"
          alt="Screenshot of “<?= $item->title() ?>”"
        >
      </figure>
      <strong><?= $item->title() ?></strong>
      <?php if ($link): ?>
        <small>
          <?= Url::short(Url::stripPath($item->link()->toString())) ?>
        </small>
      <?php endif; ?>
    <?php if ($link): ?>
      </a>
    <?php else: ?>
      </div>
    <?php endif; ?>
  <?php endif; ?>
</li>
