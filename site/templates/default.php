<?php

/** @var \Kirby\Cms\Page|DefaultPage $page */
/** @var \Kirby\Cms\Site $site */
/** @var \Kirby\Cms\Blocks $sections */

if ($page->redirect()->isTrue()) {
	$page->children()->listed()->first()?->go();
}

$headline = $page->headline()->or($page->title());
?>
<?php snippet('head') ?>
<body>
	<?php snippet('o-header') ?>
	<div class="o-main-wrapper">
		<div>
			<?php snippet('m-nav-sidebar', ['parent' => $site->children()->filter(fn (\Kirby\Cms\Page $page) => $page->isOpen())]); ?>
			<main class="o-main" aria-describedby="main-heading">
				<div class="o-main__header">
					<h1 class="a-display" id="main-heading"><?= $headline ?></h1>

					<?php if ($page->intro()->isNotEmpty()): ?>
						<div class="m-text" data-size="xlarge">
							<?= $page->intro()->kt() ?>
						</div>
					<?php endif ?>
				</div>

				<div class="o-main__dashed-line"></div>

				<div class="o-main__toc">
					<?php snippet('m-toc', ['text' => $page->text()]) ?>
				</div>

				<div class="o-main__text">
					<div class="m-text">
						<?= $page->text()->kt() ?>
					</div>
				</div>
			</main>
		</div>
	</div>
	<?php snippet('o-footer') ?>
</body>
<?php snippet('foot') ?>
