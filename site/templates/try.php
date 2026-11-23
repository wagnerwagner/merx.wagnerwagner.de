<?php

/** @var \Kirby\Cms\Page $page */

$casesPage = page('showcase');
$starterkitImage = page('home')?->heroImage()->toFile();
?>
<?php snippet('head') ?>
<body>
	<?php snippet('o-header') ?>
	<main class="o-try" aria-labelledby="try-headline">
		<header class="o-try__header">
			<h1 class="a-display" id="try-headline"><?= $page->headline()->or($page->title())->escape() ?></h1>
			<div class="m-text" data-size="xlarge">
				<?= $page->intro()->kt() ?>
			</div>
		</header>

		<div class="o-try__options">
			<section class="o-try__option" aria-labelledby="starterkit">
				<?php if ($starterkitImage): ?>
					<a class="o-try__visual" href="https://starterkit.merx2.wagnerwagner.de" rel="noopener">
						<img src="<?= $starterkitImage->crop(860, 478, 'top')->url() ?>" srcset="<?= $starterkitImage->crop(860 * 2, 478 * 2, 'top')->url() ?> 2x" width="860" height="478" alt="<?= $starterkitImage->alt()->escape() ?>">
					</a>
				<?php endif ?>
				<h2 class="a-heading" data-size="large" id="starterkit">Starterkit</h2>
				<div class="m-text" data-size="medium">
					<?= $page->starterkit()->kt() ?>
				</div>
				<div class="o-try__buttons">
					<a class="a-button" data-size="large" data-kind="brand" href="https://starterkit.merx2.wagnerwagner.de" rel="noopener">Demo shop&ensp;→</a>
					<a class="a-button" data-size="large" href="https://github.com/wagnerwagner/merx-starterkit/archive/refs/heads/develop/v2.zip" rel="noopener">Download&ensp;↓</a>
					<a class="a-button" data-kind="secondary" data-variant="text" href="https://github.com/wagnerwagner/merx-starterkit/tree/develop/v2" rel="noopener">Source code</a>
				</div>
			</section>

			<section class="o-try__option" aria-labelledby="plugin">
				<figure class="m-code o-try__visual">
					<figcaption>Terminal</figcaption>
					<pre><code class="language-bash"># with Composer
composer require wagnerwagner/merx

# or as a Git submodule
git submodule add https://github.com/wagnerwagner/merx.git site/plugins/merx</code></pre>
				</figure>
				<h2 class="a-heading" data-size="large" id="plugin">Your own Kirby site</h2>
				<div class="m-text" data-size="medium">
					<?= $page->plugin()->kt() ?>
				</div>
				<div class="o-try__buttons">
					<a class="a-button" data-size="large" data-kind="brand" href="<?= page('guide/getting-started/installation')?->url() ?>">Installation&ensp;→</a>
					<a class="a-button" data-size="large" href="https://github.com/wagnerwagner/merx/archive/refs/heads/develop/v2.zip" rel="noopener">Download&ensp;↓</a>
					<a class="a-button" data-kind="secondary" data-variant="text" href="https://github.com/wagnerwagner/merx/tree/develop/v2" rel="noopener">Source code</a>
				</div>
			</section>
		</div>

		<?php if ($casesPage): ?>
			<section class="o-try__showcase" aria-labelledby="showcase">
				<div>
					<h2 class="a-display" id="showcase">Running online shops since April 2019</h2>
					<?php if ($page->showcase()->isNotEmpty()): ?>
						<div class="m-text" data-size="large">
							<?= $page->showcase()->kt() ?>
						</div>
					<?php endif ?>
				</div>
				<ul>
					<?php foreach ($casesPage->children()->listed()->shuffle()->limit(4) as $item): ?>
						<li>
							<?php snippet('m-case-small', ['item' => $item]) ?>
						</li>
					<?php endforeach ?>
				</ul>
				<div>
				<a class="a-button" data-variant="text" data-size="large" href="<?= $casesPage->url() ?>">More examples&ensp;→</a>
				</div>
			</section>
		<?php endif ?>

		<?php if ($page->faq()->isNotEmpty()): ?>
			<section class="o-try__faq" aria-labelledby="faq">
				<h2 class="a-heading" data-size="xlarge" id="faq">Frequently asked questions</h2>
				<dl>
					<?php foreach ($page->faq()->toStructure() as $item): ?>
						<div>
							<dt class="a-heading" data-size="xsmall"><?= $item->question()->escape() ?></dt>
							<dd class="m-text" data-size="medium"><?= $item->answer()->kt() ?></dd>
						</div>
					<?php endforeach ?>
				</dl>
			</section>
		<?php endif ?>
	</main>
	<?php snippet('o-footer') ?>
</body>
<?php snippet('foot') ?>
