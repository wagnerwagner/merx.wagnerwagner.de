<?php
use Kirby\Toolkit\Str;
use Wagnerwagner\Site\ApiExamples;
use Wagnerwagner\Site\Types;

if (!function_exists('formatViewFields')) {
	function formatViewFields($fields)
	{
		$result = [];
		foreach ($fields as $key => $value) {
			if (is_array($value)) {
				// Nested array: format as "key<value1, value2, ...>"
				$result[] = $key . '&lt;' . implode(', ', $value) . '&gt;';
			} else {
				// String value: use the value directly (ignore numeric keys)
				$result[] = $value;
			}
		}
		return implode(', ', $result);
	}
}
?>
<h2>Fields</h2>
<p>The following fields are available in the <?= $page->title() ?> model and can be fetched with the select parameter.</p>
<div class="m-table">
	<table>
		<thead>
			<th>Name</th>
			<th>Return type</th>
		</thead>
		<?php foreach ($page->fields()->yaml() as $key => $field): ?>
			<tr>
				<td><?= $key ?></td>
				<td><?= (new Types($field))->toHtml(api: true) ?></td>
			</tr>
		<?php endforeach ?>
	</table>
</div>

<h2>Views</h2>
<p>The following fields are available in the <?= $page->title() ?> model and can be fetched with the view parameter.</p>
<div class="m-table">
	<table>
		<thead>
			<th>Name</th>
			<th>Fields</th>
		</thead>
		<?php foreach ($page->views()->yaml() as $key => $view): ?>
			<tr>
				<td><?= $key ?></td>
				<td><?= formatViewFields($view) ?></td>
			</tr>
		<?php endforeach ?>
	</table>
</div>

<h2>Type</h2>
<p><?= $page->referenceClass()->toHtml() ?></p>

<?php if ($examples = $page->exampleResponses()): ?>
	<h2>Examples</h2>
	<p>The <?= $page->title() ?> model of an example cart, in each of its views.</p>
	<?php foreach ($examples as $view => $example): ?>
		<figure class="m-code">
			<figcaption><?= $example['request'] ?? 'View: ' . $view ?></figcaption>
			<pre><code class="language-json"><?= Str::esc(ApiExamples::json($example['data']), 'html') ?></code></pre>
		</figure>
	<?php endforeach ?>
<?php endif ?>
