<?php
/** @var \Kirby\Cms\Page $item */

use Kirby\Cms\Url;

$link = $item->link()->isNotEmpty() ? $item->link()->toString() : null;
$screenshot = $item->screenshot()->toFile();

if ($screenshot === null) {
	return;
}
?>
<?php if ($link): ?>
	<a class="m-case-small" href="<?= $item->link() ?>" rel="noopener">
<?php else: ?>
	<div class="m-case-small">
<?php endif ?>
	<figure>
		<img
			src="<?= $screenshot->thumb([
				'width' => 740,
				'height' => round(740 * 0.7 * 1.5),
				'crop' => 'top',
			])->url() ?>"
			srcset="<?= $screenshot->thumb([
				'width' => 740 * 2,
				'height' => round(740 * 0.7 * 1.5 * 2),
				'crop' => 'top',
			])->url() ?> <?= 740 * 2 ?>w"
			sizes="
				(min-width: 50em) 46.25em,
				calc(100vw - 2rem)
			"
			width="740"
			height="<?= round(740 * 0.7 * 1.5) ?>"
			loading="lazy"
			alt="Screenshot of “<?= $item->title() ?>”"
		>
		<figcaption>
			<strong><?= $item->title() ?></strong>
			<?= $item->year()->isNotEmpty() ? '<small>' . $item->year() . '</small>' : '' ?>
			<br>
			<span>
				<?php if ($link === null): ?>
					Website is no longer available online.
				<?php else: ?>
					<em><?= Url::short(Url::stripPath($item->link()->toString())) ?></em>
				<?php endif ?>
			</span>
		</figcaption>
	</figure>
<?php if ($link): ?>
	</a>
<?php else: ?>
	</div>
<?php endif ?>
