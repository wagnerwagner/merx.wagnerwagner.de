<h2>Example response</h2>
<figure class="m-code">
	<figcaption>https://shop.test/api/<?= $page->pattern() ?></figcaption>
	<div class="m-code">
		<pre><code class="language-json"><?= $page->parent()->parent()->apiRequest(path: $page->pattern(), method: $page->method()) ?></code></pre>
	</div>
</figure>

<?php if ($resolvedApiModel = $page->parent()->parent()->resolveApiModel($page->class())): ?>
	<h2>Example response</h2>
	<figure class="m-code">
		<pre><code class="language-json"><?= json_encode($resolvedApiModel, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?></code></pre>
	</figure>
<?php endif ?>
