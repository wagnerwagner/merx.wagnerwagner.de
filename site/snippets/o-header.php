<?php

use Kirby\Cms\Html;

/** @var \Kirby\Cms\Site $site */
?>
<header class="o-header">
	<div class="o-header__center">
		<a href="<?= $site->url() ?>" class="a-logo-header">
			<img src="<?= url('assets/images/logo.svg') ?>" width="64" height="19" alt="<?= $site->title() ?>">
		</a>
		<?php snippet('m-nav-main') ?>
		<?= Html::tag('button', [snippet('a-icon', ['name' => 'search', 'weight' => 700], true)], [
			'class' => 'a-nav-item-primary',
			'data-state' => page('search')->isOpen() ? 'open' : null,
			'popovertarget' => 'dialog-search',
			'popovertargetaction' => 'toggle',
		]) ?>
		<div class="m-search-dialog" id="dialog-search" popover="auto">
			<div>
				<?php snippet('m-search') ?>
			</div>
		</div>
	</div>
</header>
