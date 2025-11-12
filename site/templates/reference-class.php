<?php

/** @var ReferenceClassPage $page */
$hasTertiaryNav = true;
?>
<?php snippet('head') ?>
<body>
	<?php snippet('o-header') ?>
	<div class="o-main-wrapper" <?= attr(['data-layout' => $hasTertiaryNav ? 'ref' : null]) ?>>
		<div>
			<?php snippet('m-nav-secondary', ['parent' => $page->parents()->filter(fn (\Kirby\Cms\Page $page) => $page->depth() === 1)]); ?>
			<?php snippet('m-nav-tertiary', ['parent' => $page]); ?>

			<main class="o-reference" aria-label="<?= $page->title() ?>">
				<h1><?= $page->headline()->isNotEmpty() ? $page->headline() : $page->title() ?></h1>
				<div class="m-text" data-size="large">
					<?= $page->text()->kt() ?>
				</div>
				<nav class="toc">
					<div>
						<h4>Table of Contents</h4>
						<ul>
							<?php foreach($page->children()->listed() as $classMethod): ?>
								<li>
									<a href="<?= $classMethod->url() ?>"><?= $classMethod->title() ?></a>
								</li>
							<?php endforeach; ?>
						</ul>
					</div>
				</nav>
			</main>
		</div>
	</div>
	<?php snippet('o-footer') ?>
</body>
<?php snippet('foot') ?>
