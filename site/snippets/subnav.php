<?php if ($pages->findOpen()->hasListedChildren()): ?>
<nav class="subnav">
  <ul>
  <?php foreach($pages->findOpen()->children()->listed() as $item): ?>
    <li>
      <a class="<?= $item->isActive() ? 'is-active' : '' ?>" href="<?= $item->url() ?>"><?= $item->title() ?></a>
      <ul>
      <?php foreach($item->listedChildren() as $item): ?>
        <li>
          <a class="<?= $item->isOpen() ? 'is-active' : '' ?>" href="<?= $item->url() ?>"><?= $item->title() ?></a>
        </li>
      <?php endforeach; ?>
      </ul>
    </li>
  <?php endforeach; ?>
  </ul>
</nav>
<?php endif; ?>