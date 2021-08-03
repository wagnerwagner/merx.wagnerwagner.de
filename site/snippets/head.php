<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title><?= $page->seoTitle() ?></title>
    <link rel="stylesheet" href="<?= timestampedAsset('assets/css/style.css') ?>">
    <script src="<?= timestampedAsset('assets/js/script.js') ?>" defer></script>
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <?php if (in_array($page->intendedTemplate()->name(), ['order'])): ?>
      <meta name="robots" content="noindex, follow">
    <?php else: ?>
      <meta name="description" content="<?= $site->metaDescription() ?>">
      <meta property="og:description" content="<?= $site->metaDescription() ?>">
      <meta property="og:image" content="<?= $site->openGraphImage()->toFile()->crop(1200, 630)->url() ?>">
      <meta property="og:site_name" content="<?= $site->title() ?>">
      <meta property="og:url" content="<?= $page->url() ?>">
      <meta property="og:type" content="website">
      <meta property="og:title" content="<?= $page->title() ?>">
    <?php endif; ?>
  </head>
