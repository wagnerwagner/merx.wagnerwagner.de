<?php
/** @var \Wagnerwagner\Site\ReferencePageAbstract $page */
/** @var \Kirby\Cms\App $kirby */

use Kirby\Toolkit\Str;
use Wagnerwagner\Site\Type;
use Wagnerwagner\Site\Types;

function formatViewFields($fields) {
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
};
?>
<main class="o-reference" aria-label="<?= $page->title() ?>">
	<h1 class="a-display"><?= $page->headline()->isNotEmpty() ? $page->headline() : $page->title() ?></h1>

	<?php if (is_string($page->summary())): ?>
		<div class="m-text" data-size="xlarge">
			<p><?= kti($page->summary()) ?></p>
		</div>
	<?php endif ?>

  <?php if ($page->gitHubUrl()): ?>
    <a class="a-type" href="<?= $page->gitHubUrl() ?>">
      <?php snippet('a-icon', ['name' => 'code', 'weight' => 450]) ?>
      <?= $page->relativeFilePath() ?><?= ($page->line()) ? '#L' . $page->line() : '' ?>
    </a>
	<?php endif ?>

	<div class="m-text">
		<?php if (is_string($page->call())): ?>
			<figure class="m-code">
				<pre class="language-php"><code><?= Str::esc($page->call(), 'html') ?></code></pre>
			</figure>
		<?php endif ?>

		<?php if (gettype($page->params()) === 'array' && count($page->params()) > 0): ?>
			<?php $showDescription = count(array_filter($page->params(), fn ($param) => !empty($param['description']))) > 0; ?>
			<h2>Parameters</h2>
			<div class="m-table">
				<table>
					<thead>
						<th>Name</th>
						<th>Type</th>
						<th>Default</th>
						<?php if ($showDescription): ?>
							<th>Description</th>
						<?php endif ?>
					</thead>
					<?php foreach ($page->params() as $param): ?>
						<tr>
							<td><code class="a-type"><?= $param['name'] ?></code></td>
							<td><?= $param['types']?->toHtml(short: true) ?></td>
							<td><code class="a-type"><?= $param['defaultValue'] === null ? '-' : $param['defaultValue'] ?></code></td>
							<?php if ($showDescription): ?>
								<td><?= kt($param['description']) ?></td>
							<?php endif ?>
						</tr>
					<?php endforeach ?>
				</table>
			</div>
		<?php endif ?>

    <?php if ($page instanceof ReferenceTranslationPage): ?>
      <h2>Translation strings</h2>
      <div class="m-table">
        <table>
          <thead>
            <th>Language</th>
            <th>Translation</th>
          </thead>
          <?php foreach ($page->translationStrings() as $langKey => $translationString): ?>
            <tr>
              <td><?= $langKey ?></td>
              <td><?= $translationString ?></td>
            </tr>
          <?php endforeach ?>
        </table>
      </div>
    <?php endif ?>

		<?php if ($page instanceof ReferenceOptionPage): ?>
			<h2>Default value</h2>
			<?= (new Type($page->defaultValue()))->toHtml() ?>

			<h2>Type</h2>
			<p><?= $page->type()->toHtml() ?></p>
		<?php endif ?>

		<?php if (($returnTypes = $page->returnTypes()) && is_countable($returnTypes) && count($returnTypes) > 0): ?>
      <?php if ($page instanceof ReferenceApiRoutePage && ($returnType = $page->returnType()) && $returnType->getDataType() === 'class'): ?>
        <h2>API model</h2>
        <p>
          <?= $returnType->toHtml(api: true) ?>
        </p>
      <?php endif ?>

			<h2>Return type</h2>
			<p>
				<?= $returnTypes->toHtml() ?>
			</p>
			<?php if ($page->returnDescription()): ?>
				<p><?= kti($page->returnDescription()) ?></p>
			<?php endif ?>
		<?php endif ?>

		<?php if (($exceptions = $page->exceptions()) && is_countable($exceptions) && count($exceptions) > 0): ?>
			<h2>Exceptions</h2>

			<div class="m-table">
				<table>
					<thead>
						<th>Type</th>
						<th>Description</th>
					</thead>
					<?php foreach ($exceptions as $exception): ?>
						<tr>
							<td><?= $exception['type']->toHtml() ?></td>
							<td><?= $exception['description'] ?></td>
						</tr>
					<?php endforeach ?>
				</table>
			</div>
		<?php endif ?>

		<?php if (is_string($page->description())): ?>
			<h2>Description</h2>

			<p><?= markdown($page->description()) ?></p>
		<?php endif ?>

		<?php if ($page->class() instanceof Type): ?>
			<h2>Parent class</h2>
			<p>
				<?= $page->class()->toHtml() ?>
				<?php if (((string)$page->declaringClass()) !== ((string)$page->class())): ?>
					inherited from <?= $page->declaringClass()->toHtml() ?>
				<?php endif ?>
			</p>
		<?php endif ?>

    <?php if ($page instanceof ReferenceApiModelPage): ?>
      <h2>Fields</h2>
			<p>The following fields are available in the <?= $page->title() ?> model and can be fetched with the select parameter.</p>
      <div class="m-table">
        <table>
          <thead>
            <th>Name</th>
            <th>Return type</th>
          </thead>
          <?php foreach ($page->fields()->value() as $key => $field): ?>
						<?php
							$reflector = new ReflectionFunction($field);
							$returnType = $reflector->getReturnType();
						?>
            <tr>
              <td><?= $key ?></td>
              <td><?= (new Types($returnType, $reflector))->toHtml(api: true) ?></td>
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
          <?php foreach ($page->views()->value() as $key => $view): ?>
            <tr>
              <td><?= $key ?></td>
              <td><?= formatViewFields($view) ?></td>
            </tr>
          <?php endforeach ?>
        </table>
      </div>

      <h2>Type</h2>
      <p><?= $page->referenceClass()->toHtml() ?></p>
    <?php endif ?>

    <?php if ($page instanceof ReferenceBlueprintPage): ?>
      <h2>Default blueprint</h2>
			<figure class="m-code">
      	<pre class="language-yaml"><code><?= $page->blueprintFileContent() ?></code></pre>
			</figure>
    <?php endif ?>

		<?php if ($page->examples()->isNotEmpty()): ?>
			<h2>Examples</h2>
			<?= $page->examples()->kt() ?>
		<?php endif ?>


		<?php if ($page instanceof ReferenceApiRoutePage): ?>
			<h2>Example response</h2>
			<figure class="m-code">
				<figcaption>https://shop.test/api/<?= $page->pattern() ?></figcaption>
				<div class="m-code">
					<pre><code class="language-json"><?= $page->parent()->parent()->apiRequest(path: $page->pattern(), method: $page->method()) ?></code></pre>
				</div>
			</figure>
		<?php endif ?>

		<?php if ($page instanceof ReferenceApiModelPage && ($resolvedApiModel = $page->parent()->parent()->resolveApiModel($page->class()))): ?>
			<h2>Example response</h2>
			<figure class="m-code">
				<pre><code class="language-json"><?= json_encode($resolvedApiModel, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?></code></pre>
			</figure>
		<?php endif ?>
	</div>
</main>
