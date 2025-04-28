<nav class="toc">
  <div>
    <h4>Table of Contents</h4>
    <ul>
      <?php foreach($sections as $section): ?>
        <li>
          <a href="#<?= $section->id() ?>"><?= $section->title() ?></a>
        </li>
      <?php endforeach; ?>
    </ul>
  </div>
</nav>
