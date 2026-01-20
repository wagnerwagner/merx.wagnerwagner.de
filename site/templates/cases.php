<?php
if ($page->redirect()->isTrue()) {
	$page->children()->listed()->first()?->go();
}
?>
<?php snippet('head') ?>
<body class="l-cases">
	<?php snippet('o-header') ?>
	<main class="o-cases">
		<div class="o-cases__box">
			<div class="o-cases__title">
				<h1 class="a-display">
					Online shops made with Kirby, Merx and <?= snippet('a-icon', ['name' => 'heart', 'weight' => 700]) ?>
				</h1>
			</div>
			<div class="o-cases__grid">
				<?php foreach($page->children()->listed() as $item): ?>
					<?php snippet('m-case', compact('item')); ?>
				<?php endforeach; ?>
			</div>
			<ul>
				<?php foreach($page->children()->unlisted()->shuffle()->limit(6) as $item): ?>
					<?php snippet('case-list-item', compact('item')) ?>
				<?php endforeach; ?>
			</ul>
			<footer>
				<?= $page->footerText()->kt() ?>
			</footer>
		</div>
	</main>
	<?php snippet('o-footer') ?>
</body>
<?php snippet('foot') ?>
