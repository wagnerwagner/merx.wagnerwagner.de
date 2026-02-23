<?php
/** @var \Wagnerwagner\Site\ReferencePageAbstract $page */
/** @var \Kirby\Cms\App $kirby */

use Wagnerwagner\Site\Type;
?>
<main class="o-reference" aria-label="<?= $page->title() ?>">
	<h1 class="a-display"><?= $page->headline()->isNotEmpty() ? $page->headline() : $page->title() ?></h1>

	<?php if (is_string($page->summary())): ?>
		<div class="m-text" data-size="xlarge">
			<p><?= kti($page->summary()) ?></p>
		</div>
	<?php endif ?>

	<?php if ($page->gitHubUrl()): ?>
		<a class="a-type" href="<?= $page->gitHubUrl() ?>">
			<?php snippet('a-icon', ['name' => 'code', 'weight' => 450]) ?>
			<?= $page->relativeFilePath() ?><?= ($page->line()) ? '#L' . $page->line() : '' ?>
		</a>
	<?php endif ?>

	<div class="m-text">
		<?php if (is_string($page->call())): ?>
			<?php snippet('reference-parts/call') ?>
		<?php endif ?>

		<?php if (gettype($page->params()) === 'array' && count($page->params()) > 0): ?>
			<?php snippet('reference-parts/params') ?>
		<?php endif ?>

		<?php if ($page instanceof ReferenceTranslationPage): ?>
			<?php snippet('reference-parts/translation-strings') ?>
		<?php endif ?>

		<?php if ($page instanceof ReferenceOptionPage): ?>
			<?php snippet('reference-parts/option') ?>
		<?php endif ?>

		<?php if (($returnTypes = $page->returnTypes()) && is_countable($returnTypes) && count($returnTypes) > 0): ?>
			<?php snippet('reference-parts/return-type', compact('returnTypes')) ?>
		<?php endif ?>

		<?php if (($exceptions = $page->exceptions()) && is_countable($exceptions) && count($exceptions) > 0): ?>
			<?php snippet('reference-parts/exceptions', compact('exceptions')) ?>
		<?php endif ?>

		<?php if (is_string($page->description())): ?>
			<?php snippet('reference-parts/description') ?>
		<?php endif ?>

		<?php if ($page->class() instanceof Type): ?>
			<?php snippet('reference-parts/parent-class') ?>
		<?php endif ?>

		<?php if ($page instanceof ReferenceApiModelPage): ?>
			<?php snippet('reference-parts/api-model') ?>
		<?php endif ?>

		<?php if ($page instanceof ReferenceBlueprintPage): ?>
			<?php snippet('reference-parts/blueprint') ?>
		<?php endif ?>

		<?php if ($page->examples()->isNotEmpty()): ?>
			<?php snippet('reference-parts/examples') ?>
		<?php endif ?>

		<?php if ($page instanceof ReferenceApiRoutePage): ?>
			<?php snippet('reference-parts/api-route') ?>
		<?php endif ?>
	</div>

	<?php if ($page->children()->listed()): ?>
		<?php snippet('reference-parts/children') ?>
	<?php endif ?>
</main>
