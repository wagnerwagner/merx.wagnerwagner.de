<?php
$link = $item->link();
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
    <a href="<?= $link ?>" rel="noopener">
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
      <?php if (!$page->isHomePage()): ?>
        <small>
          <?= Url::short(Url::stripPath($item->link()->toString())) ?>
        </small>
      <?php endif; ?>
    </a>
  <?php endif; ?>
</li>
