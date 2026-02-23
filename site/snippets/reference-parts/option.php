<?php

use Wagnerwagner\Site\Type;
?>
<h2>Default value</h2>
<?= (new Type($page->defaultValue()))->toHtml() ?>

<h2>Type</h2>
<p><?= $page->type()->toHtml() ?></p>
