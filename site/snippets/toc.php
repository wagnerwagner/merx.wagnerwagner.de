<nav class="toc">
  <div>
    <h4>Table of Contents</h4>
    <?php if ($page->sections()->filterBy('Dim', 'true')->count() > 0): ?>
      <button class="button-toggle">show all methods</button>
    <?php endif; ?>
    <ul>
      <?php foreach($page->sections() as $section): ?>
        <li>
          <a class="<?= $section->dim()->toBool() ? 'is-dim' : '' ?>" href="#<?= $section->uid() ?>"><?= $section->title() ?></a>
        </li>
      <?php endforeach; ?>
    </ul>
  </div>
</nav>
