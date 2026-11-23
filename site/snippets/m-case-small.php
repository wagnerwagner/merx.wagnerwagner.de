<?php
/** @var \Kirby\Cms\Page $item */

use Kirby\Cms\Url;

$link = $item->link()->isNotEmpty() ? $item->link()->toString() : null;
/** @var \Kirby\Cms\File */
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
			srcset="<?= $screenshot->srcset([
				'320w' => [
					'width' => 320,
					'height' => round(320 * 0.7 * 1.5),
					'crop' => 'top',
				],
				'540w' => [
					'width' => 540,
					'height' => round(540 * 0.7 * 1.5),
					'crop' => 'top',
				],
				'740w' => [
					'width' => 740,
					'height' => round(740 * 0.7 * 1.5 * 2),
					'crop' => 'top',
				],
				'1480w' => [
					'width' => 740 * 2,
					'height' => round(740 * 2 * 0.7 * 1.5 * 2),
					'quality' => 70,
					'crop' => 'top',
				],
			]) ?>"
			width="740"
			height="<?= round(740 * 0.7 * 1.5) ?>"
			loading="lazy"
			sizes="auto"
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
