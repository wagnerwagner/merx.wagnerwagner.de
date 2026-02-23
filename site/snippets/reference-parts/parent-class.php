<h2>Parent class</h2>
<p>
	<?= $page->class()->toHtml() ?>
	<?php if (((string)$page->declaringClass()) !== ((string)$page->class())): ?>
		inherited from <?= $page->declaringClass()->toHtml() ?>
	<?php endif ?>
</p>
