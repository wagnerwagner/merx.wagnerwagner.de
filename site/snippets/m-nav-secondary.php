<?php
/** @var ?\Kirby\Cms\Page $parent */

use Kirby\Cms\Html;

$parent ??= null;

if ($parent === null) return null;
?>
<nav class="m-nav-secondary">
	<ul>
	<?php foreach($parent->children()->listed() as $item): ?>
		<li>
			<?= Html::tag('a', $item->title(), [
				'href' => $item->url(),
				'class' => 'a-nav-item-secondary',
				'data-variant' => 'category',
			]) ?>
			<ul>
			<?php foreach($item->children()->listed() as $item): ?>
				<li>
					<?= Html::tag('a', $item->title(), [
						'href' => $item->url(),
						'class' => 'a-nav-item-secondary',
						'data-state' => $item->isOpen() ? 'open' : null,
					]) ?>
				</li>
			<?php endforeach; ?>
			</ul>
		</li>
	<?php endforeach; ?>
	</ul>
</nav>
