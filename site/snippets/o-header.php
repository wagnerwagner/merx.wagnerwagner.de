<?php
/** @var \Kirby\Cms\Site $site */
?>
<header class="o-header">
	<div class="o-header__center">
		<a href="<?= $site->url() ?>" class="a-logo-header">
			<img src="<?= url('assets/images/logo.svg') ?>" width="64" height="19" alt="<?= $site->title() ?>">
		</a>
		<?php snippet('m-nav-main') ?>
		<form class="header-search" action="<?= url('search') ?>">
			<input type="search" name="q" required minlength="3" autocomplete="off" aria-label="Search">
			<button type="submit" tabindex="-1" title="Toggle Search"></button>
		</form>
	</div>
</header>
