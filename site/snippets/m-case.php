<?php

use Kirby\Cms\Url;

/** @var \Kirby\Cms\Page $item */

$link = $item->link()->isNotEmpty() ? $item->link()->toString() : null;
$screenshot = $item->screenshot()->toFile();

if ($screenshot === null) {
	return;
}
?>
<section class="m-case" id="<?= $item->uid() ?>">
	<div class="m-case__copy">
		<header>
			<h2 class="a-heading" data-size="medium">
				<?php if ($link): ?>
					<a href="<?= $item->link() ?>" rel="noopener">
				<?php endif ?>
				<?= $item->title() ?>
				<?php if ($link): ?>
					</a>
				<?php endif ?>
			</h2>
			<?php if ($item->year()->isNotEmpty()): ?>
				<small>
					Online since <?= $item->year() ?>
				</small>
			<?php endif; ?>
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
		</figure>
	<?php if ($link): ?>
		</a>
	<?php else: ?>
		</div>
	<?php endif ?>
</section>
