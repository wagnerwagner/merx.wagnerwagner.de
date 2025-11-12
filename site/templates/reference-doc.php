<?php

/** @var ReferenceClassMethodPage $page */
$hasTertiaryNav = true;
$secondaryParent = $page->parents()->filter(fn (\Kirby\Cms\Page $page) => $page->depth() === 1);
$tertiaryParent = $secondaryParent->children()->children()->filter(fn (\Kirby\Cms\Page $page) => $page->isOpen());
?>
<?php snippet('head') ?>
<body>
	<?php snippet('o-header') ?>
	<div class="o-main-wrapper" <?= attr(['data-layout' => $hasTertiaryNav ? 'ref' : null]) ?>>
		<div>
			<?php snippet('m-nav-sidebar', ['parent' => $secondaryParent, 'variant' => 'secondary']); ?>
			<?php snippet('m-nav-sidebar', ['parent' => $tertiaryParent, 'variant' => 'tertiary']); ?>
			<div>
				<?php snippet('o-reference') ?>
			</div>
		</div>
	</div>
	<?php snippet('o-footer') ?>
</body>
<?php snippet('foot') ?>
