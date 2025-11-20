<?php

/** @var ReferenceClassMethodPage $page */

use Kirby\Cms\Collection;
use Kirby\Toolkit\Obj;
use Kirby\Toolkit\Str;

$hasTertiaryNav = true;
$secondaryParent = $page;
$headline = $page->headline()->or($page->title());
$tocItems = new Collection();
foreach ($page->children()->listed() as $item) {
	$tocItems->append(new Obj([
		'id'   => '#' . $item->id(),
		'text' => trim(strip_tags($item->title())),
	]));
}
?>
<?php snippet('head') ?>
<body>
	<?php snippet('o-header') ?>
	<div class="o-main-wrapper">
		<div>
			<?php snippet('m-nav-sidebar', ['parent' => $secondaryParent, 'variant' => 'secondary']); ?>
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
					<?php snippet('m-toc', ['items' => $tocItems]) ?>
				</div>

				<div class="o-main__text">
					<div class="m-text">
						<?php foreach ($page->children()->listed() as $item): ?>
							<h2 id="<?= $item->id() ?>">
								<a href="<?= $item->url() ?>">
									<?= $item->title() ?>
								</a>
							</h2>
							<nav class="m-nav-overview" aria-labelledby="<?= $item->id() ?>">
								<ul>
									<?php foreach ($item->children()->listed() as $subitem): ?>
										<li>
											<a href="<?= $subitem->url() ?>">
												<strong><?= $subitem->title() ?></strong>
												<small><?= $subitem->summary() ?></small>
											</a>
										</li>
									<?php endforeach ?>
								</ul>
							</nav>
						<?php endforeach ?>
					</div>
				</div>
			</main>
		</div>
	</div>
	<?php snippet('o-footer') ?>
</body>
<?php snippet('foot') ?>
