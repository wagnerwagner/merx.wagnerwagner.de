<?php
/** @var \Kirby\Cms\Collection $items */
/** @var \Kirby\Cms\Field $text */
/** @var ?\Kirby\Cms\Field $title */
/** @var ?string $tag */

$items = $items ?? $text->toToc($tag ?? 'h2');
?>

<?php if($items->count() > 0): ?>
<nav aria-labelledby="toc-heading" class="m-toc">
	<h2 id="toc-heading"><?= $title ?? 'On this page' ?></h2>
	<ol>
		<?php foreach($items as $item): ?>
			<li><a class="m-toc__link" href="<?= $item->id() ?>"><?= $item->text() ?></a></li>
		<?php endforeach ?>
	</ol>
</nav>
<?php endif ?>
