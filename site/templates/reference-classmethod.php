<?php

/** @var ReferenceClassMethodPage $page */

/** @var \Kirby\Cms\Blocks $sections */
var_dump($page->reflection()->isStatic());
?>
<?php snippet('head') ?>
<body>
  <?php snippet('header') ?>
  <div class="center">
    <?php snippet('subnav'); ?>
    <main class="doc" aria-label="<?= $page->title() ?>">
      <h1><?= $page->headline()->isNotEmpty() ? $page->headline() : $page->title() ?></h1>
      <div class="text">
        <p><?= $page->summary() ?></p>

        <p><a href="<?= $page->gitHubUrl() ?>"><?= $page->filePath() ?></a></p>

        <pre class="language-php"><code><?= $page->call() ?></code></pre>

        <?php if ($page->params()): ?>
          <h2>Parameters</h2>
          <table>
            <thead>
              <th>Name</th>
              <th>Type</th>
              <th>Default</th>
              <?php if (count(array_filter($page->params(), fn ($param) => !empty($param['description']))) > 0): ?>
                <th>Description</th>
              <?php endif ?>
            </thead>
            <?php foreach ($page->params() as $param): ?>
              <tr>
                <td><code><?= $param['name'] ?></code></td>
                <td><?= $param['types']?->toHtml() ?></td>
                <td><code><?= $param['defaultValue'] === null ? '-' : $param['defaultValue'] ?></code></td>
                <?php if (!empty($param['description'])): ?>
                  <td><?= kt($param['description']) ?></td>
                <?php endif ?>
              </tr>
            <?php endforeach ?>
          </table>
        <?php endif ?>

        <?php if (($returnTypes = $page->returnTypes()) && count($returnTypes) > 0): ?>
          <h2>Return types</h2>
          <p>
            <?php foreach ($returnTypes as $returnType): ?>
              <?= $returnType->toHtml() ?>
            <?php endforeach ?>
          </p>
          <?php if ($page->returnDescription()): ?>
            <p><?= kti($page->returnDescription()) ?></p>
          <?php endif ?>
        <?php endif ?>

        <?php if ($page->exceptions()): ?>
          <h2>Exceptions</h2>

          <table>
            <thead>
              <th>Type</th>
              <th>Description</th>
            </thead>
            <?php foreach ($page->exceptions() as $param): ?>
              <tr>
                <td><?= $param['type']->toHtml() ?></td>
                <td><?= $param['description'] ?></td>
              </tr>
            <?php endforeach ?>
          </table>
        <?php endif ?>

        <?php if ($page->description()): ?>
          <h2>Description</h2>

          <p><?= markdown($page->description()) ?></p>
        <?php endif ?>

        <?php if ($page->class()): ?>
          <h2>Parent class</h2>
          <?= $page->class()->toHtml() ?>
          <?php if (((string)$page->declaringClass()) !== ((string)$page->class())): ?>
            inherited from <?= $page->declaringClass()->toHtml() ?>
          <?php endif ?>
        <?php endif ?>

        <?php if ($page->examples()->isNotEmpty()): ?>
          <h2>Examples</h2>
          <?= $page->examples()->kt() ?>
        <?php endif ?>
      </div>
    </main>
  </div>
  <?php snippet('footer') ?>
</body>
<?php snippet('foot') ?>
