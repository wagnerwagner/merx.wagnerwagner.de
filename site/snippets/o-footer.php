<?php
$docLinks = pages(['guide', 'cookbook', 'reference', 'reference/api', 'guide/security', 'reference/releases']);
$merxLinks = pages(['showcase', 'buy', 'privacy', 'license', 'contact']);
?>
<footer class="o-footer">
	<div>
		<div class="o-footer__info">
			<div class="m-text" data-size="medium">
				<p>Merx is a plugin for Kirby to create online shops. Made for people who care about simplicity, flexibility, privacy, freedom of choice and the tiny details that set a website apart from the others.</p>
				<p>
					Developed and maintained by<br>
					<a class="o-footer__logo-link" href="https://www.wagnerwagner.de">
						<img src="<?= url('assets/images/wagnerwagner.svg') ?>" loading="lazy" alt="Logo: Wagnerwagner" height="16" width="169">
					</a>
				</p>
			</div>
		</div>
		<nav class="o-footer__nav">
			<ul>
				<li>
					<strong>Docs</strong>
					<ul>
						<?php foreach ($docLinks as $item): ?>
							<li>
								<a href="<?= $item->url() ?>">
									<?=  $item->title() ?>
								</a>
							</li>
						<?php endforeach?>
					</ul>
				</li>
				<li>
					<strong>Merx</strong>
					<ul>
						<?php foreach ($merxLinks as $item): ?>
							<li>
								<a href="<?= $item->url() ?>">
									<?=  $item->title() ?>
								</a>
							</li>
						<?php endforeach?>
						<li>
							<a href="https://github.com/wagnerwagner/merx" rel="noopener" title="Merx on GitHub">
								Github <small>↗</small>
							</a>
						</li>
					</ul>
				</li>
			</ul>
		</nav>
	</div>
</footer>
