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

<?php if ($errors = $page->exampleErrors()): ?>
	<h2>Example errors</h2>
	<?php foreach ($errors as $title => $error): ?>
		<h3><?= Str::esc($title) ?></h3>
		<?php if ($error['cart'] === false): ?>
			<p>The example request, sent with an empty cart.</p>
		<?php else: ?>
			<figure class="m-code">
				<figcaption>Request body</figcaption>
				<pre><code class="language-json"><?= Str::esc(ApiExamples::json($error['body']), 'html') ?></code></pre>
			</figure>
		<?php endif ?>
		<figure class="m-code">
			<figcaption>Response</figcaption>
			<pre><code class="language-json"><?= Str::esc(ApiExamples::json($error['response']), 'html') ?></code></pre>
		</figure>
	<?php endforeach ?>
<?php endif ?>
