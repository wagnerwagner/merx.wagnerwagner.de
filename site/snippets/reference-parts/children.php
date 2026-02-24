<?php

/**
 * @var \Kirby\Cms\Page $page
 */

$page = $page ?? page();
?>
<div class="m-reference-children">
	<ul>
		<?php foreach($page->children()->listed() as $item): ?>
			<li>
				<a href="<?= $item->url() ?>">
					<span><?= $item->title() ?></span>
					<?php if (!empty($item->summary())): ?>
						<small><?= $item->summary() ?></small>
					<?php endif ?>
				</a>
			</li>
		<?php endforeach; ?>
	</ul>
</div>
