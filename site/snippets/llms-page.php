<?php
/**
 * A documentation page as Markdown, e.g. /guide/getting-started/installation.md
 *
 * Loaded by the `.md` representation of every template a documentation page
 * uses. Kirby’s page cache stores it like the HTML.
 *
 * @var \Kirby\Cms\Page $page
 */

use Kirby\Exception\NotFoundException;
use Wagnerwagner\Site\Llms;

// Pages outside the documentation, e.g. /contact, have no Markdown
if (Llms::isDocPage($page) === false) {
	throw new NotFoundException();
}

echo Llms::page($page);
