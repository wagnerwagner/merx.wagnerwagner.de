<?php

use Kirby\Cms\Html;

/** @var \Kirby\Cms\Site $site */
?>
<nav class="m-nav-main">
	<ul>
		<?php foreach($site->children()->listed() as $item): ?>
		<li>
			<?= Html::tag('a', $item->title(), [
				'href' => $item->url(),
				'class' => 'a-nav-item-primary',
				'data-state' => $item->isOpen() ? 'open' : null,
			]) ?>
		</li>
	<?php endforeach; ?>
	</ul>
	<ul class="m-nav-main__steps">
		<li>
			<?= Html::tag('a', 'Try', [
				'href' => page('try')->url(),
				'class' => 'a-nav-item-primary',
				'data-variant' => 'step',
				'data-state' => page('try')->isOpen() ? 'open' : null,
			]) ?>
		</li>
		<li>
			<?= Html::tag('a', [snippet('a-icon', ['name' => 'heart', 'weight' => 700], true)], [
				'href' => page('showcase')->url(),
				'class' => 'a-nav-item-primary',
				'data-variant' => 'step',
				'data-state' => page('showcase')->isOpen() ? 'open' : null,
			]) ?>
		</li>
		<li>
			<?= Html::tag('a', 'Buy', [
				'href' => page('buy')->url(),
				'class' => 'a-nav-item-primary',
				'data-variant' => 'step',
				'data-state' => page('buy')->isOpen() ? 'open' : null,
			]) ?>
		</li>
	</ul>
</nav>
