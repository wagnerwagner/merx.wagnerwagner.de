<?php

use Kirby\Toolkit\Str;
use Wagnerwagner\Site\ApiExamples;

/** @var ReferenceApiRoutePage $page */
?>
<?php if ($response = $page->exampleResponse()): ?>
	<h2>Example response</h2>
	<figure class="m-code">
		<figcaption><?= $page->method() ?> <?= ApiExamples::BASE_URL ?>/api/<?= $page->pattern() ?></figcaption>
		<pre><code class="language-json"><?= Str::esc(ApiExamples::json($response), 'html') ?></code></pre>
	</figure>
<?php endif ?>
