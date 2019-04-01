<section id="<?= $section->uid() ?>" class="section" aria-label="<?= $section->title() ?>">
  <h2><?= $section->title() ?></h2>
  <div class="text text--big">
    <?= $section->abstract()->kt() ?>
  </div>
  <div class="text">
    <?= $section->text()->kt() ?>
  </div>
</section>
