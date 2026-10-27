<?php
use Kirby\Toolkit\Str;

$language = $page instanceof ReferenceApiRoutePage ? 'javascript' : 'php';
?>
<figure class="m-code">
	<pre class="language-<?= $language ?>"><code><?= Str::esc($page->call(), 'html') ?></code></pre>
</figure>
