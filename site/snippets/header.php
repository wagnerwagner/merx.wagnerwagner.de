<header class="header">
  <div class="center">
    <h1>
      <a href="<?= $site->url() ?>">
        <img src="<?= url('assets/images/logo.svg') ?>" alt="<?= $site->title() ?>">
      </a>
    </h1>
    <nav class="mainnav">
      <ul>
      <?php foreach($site->children()->listed() as $item): ?>
        <li>
          <a href="<?= $item->url() ?>" class="<?= $item->isOpen() ? 'is-active' : '' ?>"><?= $item->title() ?></a>
        </li>
      <?php endforeach; ?>
      </ul>
    </nav>
    <form class="header-search" action="<?= url('search') ?>">
      <input type="search" name="q" required minlength="3" autocomplete="off">
      <button type="submit" tabindex="-1"></button>
    </form>
  </div>
</header>
