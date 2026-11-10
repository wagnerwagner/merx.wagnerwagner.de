<?php

/** @var \Kirby\Cms\Page $page */

/**
 * A feature with a visual gets a row of its own. Consecutive features
 * without one are set side by side in a grid.
 */
$groups = [];
foreach ($page->features()->toBlocks() as $feature) {
	$isGrid = $feature->content()->get('visual')->isEmpty();
	$lastKey = array_key_last($groups);

	if ($isGrid === true && $lastKey !== null && $groups[$lastKey]['isGrid'] === true) {
		$groups[$lastKey]['features'][] = $feature;
		continue;
	}

	$groups[] = [
		'isGrid' => $isGrid,
		'features' => [$feature],
	];
}
?>
<?php snippet('head') ?>
<body>
	<?php snippet('o-header') ?>
	<main class="o-release" aria-labelledby="release-title">
		<header class="o-release__hero">
			<h1 class="o-release__title" id="release-title"><?= $page->title()->escape() ?></h1>
			<?php if ($page->headline()->isNotEmpty()): ?>
				<p class="a-display"><?= $page->headline()->escape() ?></p>
			<?php endif ?>
			<div class="m-text" data-size="xlarge">
				<?= $page->intro()->kt() ?>
			</div>
			<div class="o-release__buttons">
				<a class="a-button" data-size="large" data-kind="brand" href="<?= page('guide/getting-started')?->url() ?>">Get started&ensp;→</a>
				<a class="a-button" data-kind="secondary" data-variant="text" href="<?= page('guide/upgrade-merx-1-to-2')?->url() ?>">Upgrade from Merx 1</a>
			</div>
		</header>

		<?php foreach ($groups as $group): ?>
			<?php if ($group['isGrid'] === true): ?>
				<div class="o-release__grid">
					<?php foreach ($group['features'] as $feature): ?>
						<?php snippet('m-release-feature', ['feature' => $feature]) ?>
					<?php endforeach ?>
				</div>
			<?php else: ?>
				<div class="o-release__row">
					<?php snippet('m-release-feature', ['feature' => $group['features'][0]]) ?>
				</div>
			<?php endif ?>
		<?php endforeach ?>

		<?php if ($page->more()->isNotEmpty()): ?>
			<section class="o-release__more" aria-labelledby="more">
				<h2 class="a-heading" data-size="xlarge" id="more">And much more</h2>
				<div class="m-text" data-size="medium">
					<?= $page->more()->kt() ?>
				</div>
			</section>
		<?php endif ?>

		<section class="o-release__outro" aria-labelledby="get-started">
			<h2 class="a-display" id="get-started">Get started</h2>
			<?php if ($page->outro()->isNotEmpty()): ?>
				<div class="m-text" data-size="xlarge">
					<?= $page->outro()->kt() ?>
				</div>
			<?php endif ?>
			<div class="o-release__buttons">
				<a class="a-button" data-size="large" data-kind="brand" href="https://starterkit.merx2.wagnerwagner.de" rel="noopener">Starterkit&ensp;→</a>
				<a class="a-button" data-size="large" href="<?= page('buy')?->url() ?>">Buy a license</a>
			</div>
		</section>
	</main>
	<?php snippet('o-footer') ?>
</body>
<?php snippet('foot') ?>
