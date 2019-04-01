<footer class="footer">
  <div class="center">
    <div class="footer__info">
      <h2>Merx is developed and maintained by</h2>
      <a href="https://www.wagnerwagner.de">
        <img src="<?= url('assets/images/wagnerwagner.svg') ?>" alt="Logo: Wagnerwagner">
      </a>
    </div>
    <nav class="footer__nav">
      <ul>
      <?php foreach($site->legalLinks()->toPages() as $item): ?>
        <li>
          <a href="<?= $item->url() ?>"><?= $item->title() ?></a>
        </li>
      <?php endforeach; ?>
      </ul>
    </nav>
  </div>
</footer>
