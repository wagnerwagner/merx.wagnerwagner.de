<?php

use Kirby\Toolkit\Str;
use Wagnerwagner\Site\Type;

/** @var ReferenceOptionPage $page */
$defaultValue = $page->defaultValue();
?>
<?php if ($defaultValue !== null): ?>
	<h2>Default value</h2>
	<?php if ((string)$page->type() === 'function' || str_contains($defaultValue, "\n") === true): ?>
		<figure class="m-code">
			<pre class="language-php"><code><?= Str::esc($defaultValue, 'html') ?></code></pre>
		</figure>
	<?php else: ?>
		<?= (new Type($defaultValue))->toHtml() ?>
	<?php endif ?>
<?php endif ?>

<h2>Type</h2>
<p><?= $page->type()->toHtml() ?></p>
