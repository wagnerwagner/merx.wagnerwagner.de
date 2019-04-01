<?php
use Kirby\Toolkit\Str;

$reflectionMethod = $section->reflectionMethod();
?>

<section id="<?= $section->uid() ?>" class="section" aria-label="<?= $section->title() ?>">
  <h2><?= $section->title() ?></h2>
  <div class="text text--big">
    <?= $section->abstract()->isNotEmpty() ? $section->abstract()->kt() : kirbytext($reflectionMethod->getSummary()) ?>
  </div>
  <div class="text">
    <?php if ($reflectionMethod): ?>
      <pre class=" language-php"><code class="language-php"><?= $reflectionMethod->getCode() ?></code></pre>
    <?php endif; ?>
    <?= $section->summary()->kt() ?>

    <?php if ($reflectionMethod): ?>
      <?php if ($parameters = $reflectionMethod->getParameters()): ?>
        <h3>Parameters</h3>
        <table>
          <thead>
            <tr>
              <th>Name</th>
              <th>Type</th>
              <th>Description</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach($parameters as $param): ?>
              <tr>
                <td><code><?= $param->getVariableName() ?></code></td>
                <td><code><?= $param->getType() ?></code></td>
                <td><?= kirbyTextInline($param->getDescription()) ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      <?php endif; ?>
      <?php if (Str::length($reflectionMethod->getReturnType()) > 0): ?>
        <h3>Return Type</h3>
        <p><code><?= $reflectionMethod->getReturnType() ?></code><br>
        <?php if (Str::length($reflectionMethod->getReturnDescription()) > 0): ?>
          <?= kirbytext($reflectionMethod->getReturnDescription()) ?></p>
        <?php endif; ?>
      <?php endif; ?>
      <?php if ($reflectionMethod->class !== (string)$section->className()): ?>
        <h3>Inherits from</h3>
        <?php if ($link = $reflectionMethod->getInheritsFromLink()): ?>
          <p><code><a href="<?= $link ?>"><?= $reflectionMethod->class ?></a></code></p>
        <?php else: ?>
          <p><code><?= $reflectionMethod->class ?></code></p>
        <?php endif; ?>
      <?php endif; ?>
    <?php endif; ?>

    <?= $section->text()->kt() ?>

    <?php if ($reflectionMethod): ?>
      <?php if (Str::startsWith($reflectionMethod->class, 'Kirby')): ?>
        <p><?= kirbytag('github', $reflectionMethod->getFileName(), ['line' => $reflectionMethod->getStartLine(), 'url' => 'https://github.com/getkirby/kirby']) ?></p>
      <?php else: ?>
        <p><?= kirbytag('github', $reflectionMethod->getFileName(), ['line' => $reflectionMethod->getStartLine()]) ?></p>
      <?php endif; ?>
    <?php endif; ?>
  </div>
</section>
