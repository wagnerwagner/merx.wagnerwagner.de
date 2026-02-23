<?php if ($page instanceof ReferenceApiRoutePage && ($returnType = $page->returnType()) && $returnType->getDataType() === 'class'): ?>
	<h2>API model</h2>
	<p>
		<?= $returnType->toHtml(api: true) ?>
	</p>
<?php endif ?>

<h2>Return type</h2>
<p>
	<?= $returnTypes->toHtml() ?>
</p>
<?php if ($page->returnDescription()): ?>
	<p><?= kti($page->returnDescription()) ?></p>
<?php endif ?>
