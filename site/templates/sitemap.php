<?php
header('Content-type: text/xml; charset="utf-8"');

$ignoreTemplates = array('sitemap', 'error', 'merx-license', 'orders', 'order', 'section', 'section--class-method');
$ignoreSlug = array('error', 'merx-license', 'success', 'privacy', 'license');

echo '<?xml version="1.0" encoding="utf-8"?>';
?>

<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
<?php foreach($pages->index() as $item): ?>
<?php if(in_array((string)$item->intendedTemplate(), $ignoreTemplates)) continue ?>
<?php if(in_array((string)$item->id(), $ignoreSlug)) continue ?>
  <url>
    <loc><?= html($item->url()) ?></loc>
    <lastmod><?= $item->modified('c') ?></lastmod>
  </url>
<?php endforeach ?>
</urlset>
