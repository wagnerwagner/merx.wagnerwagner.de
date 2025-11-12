<?php
/** @var ?\Kirby\Cms\Page $parent */
/** @var ?string $variant */

use Kirby\Cms\Html;

$parent ??= null;
$variant ??= null;

if ($parent === null) return null;
?>
<nav class="m-nav-sidebar" <?= attr(['data-variant' => $variant]) ?>>
	<ul>
	<?php foreach($parent->children()->listed() as $item): ?>
		<li>
			<?php if ($variant === 'tertiary'): ?>
				<?php
					$title = $item->title();
					$summary = $item->summary();
				?>
				<?= Html::tag('a', [<<<HTML
				<span>$title</span>
				<small>$summary</small>
				HTML], [
					'href' => $item->url(),
					'class' => 'a-nav-item-sidebar',
					'data-state' => $item->isOpen() ? 'open' : null,
					'data-variant' => $variant,
				]) ?>
			<?php else: ?>
				<?= Html::tag('a', $item->title(), [
					'href' => $item->url(),
					'class' => 'a-nav-item-sidebar',
					'data-variant' => 'category',
				]) ?>
				<ul>
					<?php foreach($item->children()->listed() as $item): ?>
						<li>
							<?= Html::tag('a', $item->title(), [
								'href' => $item->url(),
								'class' => 'a-nav-item-sidebar',
								'data-state' => $item->isOpen() ? 'open' : null,
							]) ?>
						</li>
					<?php endforeach; ?>
				</ul>
			<?php endif ?>
		</li>
	<?php endforeach; ?>
	</ul>
</nav>
