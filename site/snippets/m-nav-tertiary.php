<?php
/** @var ?\Kirby\Cms\Page $parent */

use Kirby\Cms\Html;

$parent ??= null;

if ($parent === null) return null;
?>
<nav class="m-nav-secondary">
	<ul>
	<?php foreach($parent->children()->listed() as $item): ?>
		<?php
		$title = $item->title();
		$summary = $item->summary();
		?>
		<li>
			<?= Html::tag('a', [<<<HTML
			<span>$title</span>
			<small>$summary</small>
			HTML], [
				'href' => $item->url(),
				'class' => 'a-nav-item-secondary',
				'data-state' => $item->isOpen() ? 'open' : null,
			]) ?>
		</li>
	<?php endforeach; ?>
	</ul>
</nav>
