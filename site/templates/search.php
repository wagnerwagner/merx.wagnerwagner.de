<?php snippet('head') ?>
<body>
  <?php snippet('header') ?>
  <main class="search" aria-label="<?= $page->title() ?>">
    <header class="search__header">
      <div>
        <h1><?= $page->title() ?></h1>
        <form action="" validate>
          <input type="search" autofocus name="q" value="<?= esc(get('q', '')) ?>" required minlength="3" autocomplete="off">
          <button type="submit"></button>
        </form>
        <?php if ($results->count() > 0): ?>
          <small><?= $results->count() === 1 ? $results->count() . ' result' : $results->count() . ' results' ?></small>
        <?php endif; ?>
      </div>
    </header>
    <ul class="search__results">
    <?php foreach($results as $item): ?>
      <li>
        <a href="<?= url($item->id()) ?>">
          <strong><?= $item->title() ?></strong>
          <small><?= $item->id() ?></small>
          <?php if ($item->excerpt()->isNotEmpty()): ?>
          <p><?= $item->excerpt() ?></p>
          <?php endif; ?>
        </a>
      </li>
    <?php endforeach; ?>
    </ul>
  </main>
  <?php snippet('footer') ?>
</body>
<?php snippet('foot') ?>
