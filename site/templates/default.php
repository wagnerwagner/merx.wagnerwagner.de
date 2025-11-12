<?php

/** @var \Kirby\Cms\Page $page */
/** @var \Kirby\Cms\Site $site */
/** @var \Kirby\Cms\Blocks $sections */

if ($page->redirect()->isTrue()) {
	$page->children()->listed()->first()?->go();
}
?>
<?php snippet('head') ?>
<body>
	<?php snippet('o-header') ?>
	<div class="o-main-wrapper">
		<div>
			<?php snippet('m-nav-secondary', ['parent' => $site->children()->filter(fn (\Kirby\Cms\Page $page) => $page->isOpen())]); ?>
			<main class="o-reference" aria-label="<?= $page->title() ?>">
				<h1 class="a-display"><?= $page->headline()->isNotEmpty() ? $page->headline() : $page->title() ?></h1>
				<div class="text">
					<?= $page->text()->kt() ?>
				</div>
				<?php if ($sections->count() > 0): ?>
					<?php snippet('toc'); ?>
					<?= $sections ?>
				<?php endif ?>
			</main>
		</div>
	</div>
	<?php snippet('o-footer') ?>
</body>
<?php snippet('foot') ?>
