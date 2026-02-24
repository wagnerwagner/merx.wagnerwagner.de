<?php

use Wagnerwagner\Site\Type;

/** @var ReferenceOptionPage $page */
?>
<?php if ($page->defaultValue()): ?>
	<h2>Default value</h2>
	<?= (new Type($page->defaultValue()))->toHtml() ?>
<?php endif ?>

<h2>Type</h2>
<p><?= $page->type()->toHtml() ?></p>
