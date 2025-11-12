<?php
/** @var \Kirby\Cms\Page $page */

use Wagnerwagner\Site\Type;

?>
<main class="o-reference" aria-label="<?= $page->title() ?>">
	<h1 class="a-display"><?= $page->headline()->isNotEmpty() ? $page->headline() : $page->title() ?></h1>

	<?php if (is_string($page->summary())): ?>
		<div class="m-text" data-size="xlarge">
			<p><?= $page->summary() ?></p>
		</div>
	<?php endif ?>

	<a class="a-link-reference" href="<?= $page->gitHubUrl() ?>">
		<?php snippet('a-icon', ['name' => 'code', 'weight' => 450]) ?>
		<?= $page->filePath() ?>
	</a>

	<div class="m-text">
		<?php if (is_string($page->call())): ?>
			<pre class="m-code language-php"><code><?= $page->call() ?></code></pre>
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
							<td><code><?= $param['name'] ?></code></td>
							<td><?= $param['types']?->toHtml() ?></td>
							<td><code><?= $param['defaultValue'] === null ? '-' : $param['defaultValue'] ?></code></td>
							<?php if ($showDescription): ?>
								<td><?= kt($param['description']) ?></td>
							<?php endif ?>
						</tr>
					<?php endforeach ?>
				</table>
			</div>
		<?php endif ?>

		<?php if (($returnTypes = $page->returnTypes()) && is_countable($returnTypes) && count($returnTypes) > 0): ?>
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
      <div class="m-table">
        <table>
          <thead>
            <th>Name</th>
            <th>Return type</th>
          </thead>
          <?php foreach ($page->fields()->value() as $key => $field): ?>
            <tr>
              <td><?= $key ?></td>
              <td><?= (new ReflectionFunction($field))->getReturnType() ?></td>
            </tr>
          <?php endforeach ?>
        </table>
      </div>

      <h2>Views</h2>
      <div class="m-table">
        <table>
          <thead>
            <th>Name</th>
            <th>Fields</th>
          </thead>
          <?php
          $formatViewFields = function($fields) use (&$formatViewFields) {
            $result = [];
            foreach ($fields as $key => $value) {
              if (is_array($value)) {
                // Nested array: format as "key (value1, value2, ...)"
                $result[] = $key . ' &lt;' . implode(', ', $value) . '&gt;';
              } else {
                // String value: use the value directly (ignore numeric keys)
                $result[] = $value;
              }
            }
            return implode(', ', $result);
          };
          ?>
          <?php foreach ($page->views()->value() as $key => $view): ?>
            <tr>
              <td><?= $key ?></td>
              <td><?= $formatViewFields($view) ?></td>
            </tr>
          <?php endforeach ?>
        </table>
      </div>

      <h2>Class</h2>
      <p><?= $page->referenceClass()->toHtml() ?></p>
    <?php endif ?>

		<?php if ($page->examples()->isNotEmpty()): ?>
			<h2>Examples</h2>
			<?= $page->examples()->kt() ?>
		<?php endif ?>
	</div>
</main>
