<?php

use Kirby\Toolkit\Str;
use Wagnerwagner\Site\ApiExamples;

/** @var \Wagnerwagner\Site\ReferencePageAbstract $page */
$examples = $page->exampleResponses()
?>
<h2>Examples</h2>
<p>The <?= $page->title() ?> model of an example cart, in each of its views.</p>
<?php foreach ($examples as $view => $example): ?>
	<figure class="m-code">
		<figcaption><?= $example['request'] ?? 'View: ' . $view ?></figcaption>
		<pre><code class="language-json"><?= Str::esc(ApiExamples::json($example['data']), 'html') ?></code></pre>
	</figure>
<?php endforeach ?>
