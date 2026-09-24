<?php

use Kirby\Toolkit\Str;

/**
 * One feature of a release page
 *
 * @var \Kirby\Cms\Block $feature
 */

$content = $feature->content();
$id = Str::slug($content->get('label'));
$link = $content->get('link');
$url = $link->isNotEmpty() ? $link->toUrl() : null;
$linkText = $content->get('linkText')->or($link->toPage()?->title())->or('Read more');
?>
<section class="m-release-feature" id="<?= $id ?>" aria-labelledby="<?= $id ?>-headline">
	<div class="m-release-feature__copy">
		<p class="m-release-feature__label"><?= $content->get('label')->escape() ?></p>
		<h2 class="a-heading" data-size="large" id="<?= $id ?>-headline"><?= $content->get('headline')->escape() ?></h2>
		<div class="m-text" data-size="medium">
			<?= $content->get('text')->kt() ?>
		</div>
		<?php if ($url): ?>
			<div>
				<a class="a-button" data-variant="text" href="<?= $url ?>"><?= $linkText->escape() ?>&ensp;→</a>
			</div>
		<?php endif ?>
	</div>
	<?php if ($content->get('visual')->isNotEmpty()): ?>
		<div class="m-release-feature__visual">
			<?= $content->get('visual')->kt() ?>
		</div>
	<?php endif ?>
</section>
