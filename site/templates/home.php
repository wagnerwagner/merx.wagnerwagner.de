
<?php snippet('head') ?>
<body>
	<?php snippet('o-header') ?>
	<main class="o-home" aria-label="<?= $page->title() ?>">
		<div class="o-hero">
			<div class="o-hero__copy">
				<div class="o-hero__tagline">
					<a class="a-button" data-variant="text" data-size="large" href="<?= url('releases/2') ?>">
						<span><strong>Merx 2:</strong> More shop. Just as simple.</span>
					</a>
				</div>
				<h1 class="a-display" data-size="large"><?= $page->heroCopy() ?></h1>
				<div class="o-hero__buttons">
					<div>
						<a class="a-button" data-size="large" data-kind="brand" href="https://starterkit.merx2.wagnerwagner.de" rel="noopener">Starterkit&ensp;→</</a>
						<a class="a-button" data-kind="secondary" data-variant="text" href="https://github.com/wagnerwagner/merx-starterkit/tree/develop/v2" rel="noopener">Source Code</a>
					</div>
					<div>
						<a class="a-button" data-size="large" data-kind="brand" href="https://github.com/wagnerwagner/merx/archive/refs/heads/develop/v2.zip" rel="noopener">Download&ensp;↓</</a>
						<a class="a-button" data-kind="secondary" data-variant="text" href="https://github.com/wagnerwagner/merx/tree/develop/v2" rel="noopener">Source Code</a>
					</div>
				</div>
			</div>
			<?php if ($heroImage = $page->heroImage()->toFile()): ?>
			<div class="o-hero__image">
				<a href="https://starterkit.merx.wagnerwagner.de" rel="noopener">
					<img src="<?= $heroImage->crop(860, 478, 'top')->url() ?>" srcset="<?= $heroImage->crop(860 * 2, 478 * 2, 'top')->url() ?> 2x" alt="<?= $heroImage->alt() ?>">
				</a>
				</div>
			<?php endif; ?>
		</div>
		<div class="o-features">
			<?php foreach ($page->features()->toStructure() as $item): ?>
				<div class="o-features__item">
					<h2 class="a-heading" data-size="small"><?= $item->headline() ?></h2>
					<?= $item->text()->kt() ?>
				</div>
			<?php endforeach; ?>
		</div>
		<?php $casesPage = page('showcase'); ?>
		<?php if ($casesPage): ?>
			<aside class="o-showcase-home">
				<h2 class="a-display">
					Online shops made with Kirby, Merx and <?= snippet('a-icon', ['name' => 'heart', 'weight' => 'fill']) ?>
				</h2>
				<ul>
					<?php foreach ($casesPage->children()->listed()->shuffle()->limit(4) as $item): ?>
						<?php snippet('m-case-small', compact('item')) ?>
					<?php endforeach; ?>
				</ul>
				<div>
					<a class="a-button" data-variant="text" data-size="large" href="<?= $casesPage->url() ?>">Show more examples →</a>
				</div>
			</aside>
		<?php endif; ?>
	</main>
	<?php snippet('o-footer') ?>
</body>
<?php snippet('foot') ?>
