<?php
/**
 * @var ?string $query
 * @var array $results
 * @var ?string $message
 * @var ?array $data
 * @var \Kirby\Toolkit\Pagination $pagination
 */
?>
<?php snippet('head') ?>
<body>
	<?php snippet('o-header') ?>
	<main class="o-search-results">
		<div>
			<header class="o-search-results__header">
				<h1 class="a-display"><?= $page->title() ?></h1>
			</header>
			<div class="o-search-results__search">
				<form class="m-search" action="<?= url('search') ?>">
					<div class="m-search__field">
						<input type="search" autofocus name="q" value="<?= esc($query, 'attr') ?>" required minlength="3" autocomplete="off">
						<div class="m-search__submit">
							<button class="a-button" data-variant="ghost" data-shape="squared" data-kind="secondary" type="submit"><?= snippet('a-icon', ['name' => 'search', 'weight' => 700]) ?>
								<span class="a-visually-hidden">Search</span>
							</button>
						</div>
					</div>
					<?php if ($message): ?>
						<div class="m-search__info"><?= $message ?></div>
					<?php endif ?>
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
					<?php if ($pagination->hasPages()): ?>
						<nav class="o-search-results__pagination" aria-label="<?= t('pagination') ?>">
							<?php snippet('a-pagination', ['pagniation' => $pagination]) ?>
						</nav>
					<?php endif ?>
				</form>
			</div>
		</div>
	</main>
	<?php snippet('o-footer') ?>
</body>
<?php snippet('foot') ?>
