<?php
/**
 * @var array $results
 * @var ?string $message
 * @var ?array $data
 */
?>
<?php snippet('head') ?>
<body>
	<?php snippet('o-header') ?>
	<main class="search" aria-label="<?= $page->title() ?>">
		<header class="search__header">
			<div>
				<h1><?= $page->title() ?></h1>
				<form action="" validate>
					<input type="search" autofocus name="q" value="<?= esc(get('q', '')) ?>" required minlength="3" autocomplete="off">
					<button type="submit"></button>
				</form>
				<?php if ($message): ?>
					<small><?= $message ?></small>
				<?php endif ?>
			</div>
		</header>
		<?php if ($data): ?>
			<ul class="m-search__results">
			<?php foreach($data as $item): ?>
				<li>
					<a href="<?= $item['url'] ?>">
						<strong><?= $item['title'] ?></strong>
						<small><?= implode(' / ', $item['breadcrumbs']) ?></small>
					</a>
				</li>
			<?php endforeach; ?>
			</ul>
		<?php endif ?>
	</main>
	<?php snippet('o-footer') ?>
</body>
<?php snippet('foot') ?>
