<?php

/** @var \Kirby\Cms\Page|DefaultPage $page */
/** @var \Kirby\Cms\Site $site */
/** @var \Kirby\Cms\Blocks $sections */

if ($page->redirect()->isTrue()) {
	$page->children()->listed()->first()?->go();
}

$headline = $page->headline()->or($page->title());
$parentPage = $site->children()->filter(fn (\Kirby\Cms\Page $page) => $page->isOpen())->first();
?>
<?php snippet('head') ?>
<body>
	<?php snippet('o-header') ?>
	<div class="o-main-wrapper">
		<div>
			<button class="a-button-hamburger" popovertarget="sidebar-navs">
				<span></span>
				<span></span>
				<span></span>
				<i><?= $parentPage->title() ?></i>
			</button>
			<div class="m-nav-sidebars" id="sidebar-navs">
				<div>
					<?php snippet('m-nav-sidebar', ['parent' => $parentPage]); ?>
				</div>
			</div>
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
