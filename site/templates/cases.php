<?php
/** @var \Kirby\Cms\Page $page */
?>
<?php snippet('head') ?>
<body class="l-cases">
	<?php snippet('o-header') ?>
	<main class="o-cases">
		<div class="o-cases__box">
			<div class="o-cases__title">
				<h1 class="a-display" style="max-inline-size: 20ch;">
					Online shops made with Kirby, Merx and <?= snippet('a-icon', ['name' => 'heart', 'weight' => 'fill']) ?>
				</h1>
			</div>
			<div class="o-cases__big">
				<?php foreach ($page->children()->listed() as $item): ?>
					<?php snippet('m-case', compact('item')); ?>
				<?php endforeach; ?>
			</div>
			<ul class="o-cases__small">
				<?php foreach ($page->children()->unlisted()->shuffle()->limit(6) as $item): ?>
					<li><?php snippet('m-case-small', compact('item')) ?></li>
				<?php endforeach; ?>
			</ul>
			<footer class="o-cases_footer">
				<div class="m-text" style="max-inline-size: 50ch">
					<?= $page->footerText()->kt() ?>
				</div>
			</footer>
		</div>
	</main>
	<?php snippet('o-footer') ?>
</body>
<?php snippet('foot') ?>
