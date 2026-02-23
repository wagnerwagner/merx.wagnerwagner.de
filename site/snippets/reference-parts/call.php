<?php
use Kirby\Toolkit\Str;
?>
<figure class="m-code">
	<pre class="language-php"><code><?= Str::esc($page->call(), 'html') ?></code></pre>
</figure>
