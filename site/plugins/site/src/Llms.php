<?php

namespace Wagnerwagner\Site;

use Kirby\Cms\Page;
use Kirby\Exception\InvalidArgumentException;
use Kirby\Toolkit\A;
use Kirby\Toolkit\Str;
use Kirby\Uuid\Uuid;

/**
 * Builds `/llms.txt` and the Markdown representation (`…/page.md`)
 * of every documentation page.
 *
 * `llms.txt` is only an index: it links every documentation page as
 * Markdown so an LLM can fetch the pages it actually needs, instead of
 * parsing HTML or swallowing the whole documentation at once.
 *
 * @see https://llmstxt.org
 */
class Llms
{
	/**
	 * Deepest heading level used in `llms.txt`. Pages below it are
	 * listed as links under their closest ancestor heading.
	 */
	private const MAX_HEADING = 4;

	/**
	 * Top level pages that make up the documentation, in the order in
	 * which they appear in `llms.txt`. Everything else on the site
	 * (shop, showcase, legal pages) is not documentation and therefore
	 * neither indexed nor available as Markdown.
	 */
	public const SECTIONS = ['guide', 'reference', 'cookbook'];

	private static function apiModel(Page $page): string
	{
		$fields = [];

		foreach ($page->fields()->yaml() as $key => $field) {
			$fields[] = [$key, static::inlineCode((string)(new Types($field)))];
		}

		$views = [];

		foreach ($page->views()->yaml() as $key => $view) {
			$names = [];

			foreach ($view as $name => $value) {
				$names[] = is_array($value)
					? $name . '<' . implode(', ', $value) . '>'
					: $value;
			}

			$views[] = [$key, static::inlineCode(implode(', ', $names))];
		}

		return static::join([
			'## Fields',
			'Available in the ' . static::text($page->title())
				. ' model, can be fetched with the `select` parameter.',
			static::table(['Name', 'Return type'], $fields),
			'## Views',
			'Available in the ' . static::text($page->title())
				. ' model, can be fetched with the `view` parameter.',
			static::table(['Name', 'Fields'], $views),
			'## Type',
			static::inlineCode((string)$page->referenceClass()),
		]);
	}

	private static function apiRoute(Page $page): string|null
	{
		$api = $page->parent()->parent();

		try {
			$response = $api->apiRequest(path: $page->pattern(), method: $page->method());
		} catch (\Throwable) {
			$response = null;
		}

		if ($response) {
			return static::join([
				'## Example response',
				'`' . $page->method() . ' /api/' . $page->pattern() . '`',
				static::code((string)$response, 'json'),
			]);
		}

		try {
			$model = $api->resolveApiModel($page->class());
		} catch (\Throwable) {
			$model = null;
		}

		if ($model) {
			return static::join([
				'## Example response',
				static::code(
					json_encode($model, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
					'json'
				),
			]);
		}

		return null;
	}

	/**
	 * Reads a single attribute out of a raw html attribute string.
	 */
	private static function attr(string $attributes, string $name): string|null
	{
		if (preg_match('!\b' . $name . '\s*=\s*"([^"]*)"!', $attributes, $matches) === 1) {
			return $matches[1];
		}

		return null;
	}

	private static function blockquote(string|null $string): string|null
	{
		if (empty($string) === true) {
			return null;
		}

		$lines = Str::split(trim($string), PHP_EOL, 0);

		return implode(PHP_EOL, A::map(
			$lines,
			fn (string $line) => rtrim('> ' . $line)
		));
	}

	/**
	 * `<callout theme="caution">` becomes a GitHub alert, which is what
	 * the callout box is modelled after.
	 *
	 * @see /site/snippets/m-callout.php
	 */
	private static function callouts(string $text): string
	{
		return preg_replace_callback(
			'!<callout([^>]*)>(.*?)</callout>!s',
			function (array $matches): string {
				$theme = static::attr($matches[1], 'theme') ?? 'note';
				return static::blockquote(
					'[!' . Str::upper($theme) . ']' . PHP_EOL . trim($matches[2])
				);
			},
			$text
		);
	}

	/**
	 * Links the children of a page so an agent can walk the tree
	 * without going back to `llms.txt`.
	 */
	private static function children(Page $page): string|null
	{
		$children = $page->children()->listed();

		if ($children->isEmpty()) {
			return null;
		}

		return static::join([
			'## Pages',
			static::join(A::map($children->values(), fn ($child) => static::indexLink($child)), PHP_EOL),
		]);
	}

	/**
	 * Removes characters that are invisible in html but confuse a
	 * tokenizer: soft hyphens, zero width spaces, non breaking spaces.
	 */
	private static function clean(string $string): string
	{
		$string = str_replace(["\r\n", "\r"], PHP_EOL, $string);

		return preg_replace(
			['!\x{00ad}|\x{200b}|\x{2060}|\x{feff}!u', '!\x{00a0}!u'],
			['', ' '],
			$string
		);
	}

	private static function code(string $code, string|null $language = null, string|null $title = null): string
	{
		$fence = '```' . ($language ?? '');

		if (empty($title) === false) {
			$fence .= ' title="' . $title . '"';
		}

		return $fence . PHP_EOL . trim(static::clean($code)) . PHP_EOL . '```';
	}

	/**
	 * `<code caption="…">` boxes and the older `(filename: …)` tag both
	 * caption the fenced code block that follows. Both become the title
	 * of the fence itself, so no code is touched.
	 */
	private static function codeBoxes(string $text): string
	{
		// <code caption="/site/config/config.php">```php … ```</code>
		$text = preg_replace_callback(
			'!<code([^>]*)>\s*\R+```([^\s`]*)(.*?)```\s*\R+</code>!s',
			function (array $matches): string {
				$caption = static::attr($matches[1], 'caption');
				return static::code($matches[3], $matches[2], $caption);
			},
			$text
		);

		// Unwrap <code> boxes without a fenced block inside.
		$text = preg_replace('!</?code[^>]*>!', '', $text);

		// (filename: site/config/config.php) above a fenced block
		return preg_replace_callback(
			'!^\(filename:\s*([^)]*)\)\s*\R+```([^\s`]*)!m',
			fn (array $matches): string => '```' . $matches[2]
				. ' title="' . trim($matches[1]) . '"',
			$text
		);
	}

	/**
	 * The one line description used behind a link in `llms.txt`.
	 */
	private static function description(Page $page): string|null
	{
		$description = null;

		// Reference pages carry their summary in the DocBlock.
		if (is_string($page->summary())) {
			$description = $page->summary();
		}

		$description ??= $page->shortdescription()->or($page->intro())->value();

		if (empty($description)) {
			return null;
		}

		// A description has to stay on a single line.
		$description = static::text($description);
		$description = preg_replace('!\s+!', ' ', $description);

		return trim($description);
	}

	private static function exceptions(array $exceptions): string
	{
		$rows = [];

		foreach ($exceptions as $exception) {
			$rows[] = [
				static::inlineCode((string)$exception['type']),
				static::text($exception['description'] ?? ''),
			];
		}

		return static::join(['## Exceptions', static::table(['Type', 'Description'], $rows)]);
	}

	/**
	 * Returns the documentation page for the given id or `null` if the
	 * id does not point at a documentation page.
	 */
	public static function find(string $id): Page|null
	{
		$page = page($id);

		if ($page === null || static::isDocPage($page) === false) {
			return null;
		}

		return $page;
	}

	/**
	 * Resolves the `@see` tag of a DocBlock. It is used for three
	 * different things in Merx: a link into the guide, a reference to
	 * another class or member, and a plugin file path. Only the first
	 * one may be linked as a documentation page.
	 */
	private static function guide(ReferencePageAbstract $page): string|null
	{
		$guide = trim((string)$page->guide()?->value());

		if ($guide === '') {
			return null;
		}

		// A class or member reference, e.g. `\Wagnerwagner\Merx\ListItem`
		// or `Merx::$protectedOrderFields`.
		if (Str::contains($guide, '\\') === true || Str::contains($guide, '::') === true) {
			$class = ltrim(Str::before($guide, '::') ?: $guide, '\\');
			$name  = A::last(Str::split($class, '\\')) ?? '';
			$label = static::inlineCode(ltrim($guide, '\\'));

			if ($target = static::find('reference/classes/' . Str::kebab($name))) {
				return '- See also: [' . $label . '](' . static::url($target) . ')';
			}

			return '- See also: ' . $label;
		}

		if ($target = static::find(trim($guide, '/'))) {
			return '- Read more: <' . static::url($target) . '>';
		}

		// A plugin file path, or a link that no longer resolves.
		return '- See also: ' . static::inlineCode($guide);
	}


	/*
	|--------------------------------------------------------------------
	| llms.txt
	|--------------------------------------------------------------------
	 */

	/**
	 * Builds the complete `llms.txt` index.
	 */
	public static function index(): string
	{
		$blocks = [static::preamble()];

		foreach (static::SECTIONS as $slug) {
			if ($section = page($slug)) {
				$blocks[] = static::indexSection($section);
			}
		}

		return static::join($blocks) . PHP_EOL;
	}

	/**
	 * Walks the page tree. A page that has listed children becomes a
	 * heading, a page without becomes a link. Pages deeper than
	 * `MAX_HEADING` are listed as links under the closest heading.
	 */
	private static function indexChildren(Page $parent, int $level): string
	{
		$blocks = [];
		$links  = [];

		foreach ($parent->children()->listed() as $child) {
			$children = $child->children()->listed();

			// Leaf page, or too deep to get its own heading:
			// list it as a link next to its siblings.
			if ($children->isEmpty() || $level > static::MAX_HEADING) {
				$links[] = static::indexLink($child);

				// A page too deep for a heading still needs its
				// children listed, otherwise they are lost.
				if ($level > static::MAX_HEADING) {
					foreach ($children as $grandChild) {
						$links[] = static::indexLink($grandChild);
					}
				}

				continue;
			}

			// Flush collected links before opening a new heading so
			// the document order stays intact.
			if ($links !== []) {
				$blocks[] = static::join($links, PHP_EOL);
				$links = [];
			}

			// Redirecting pages have no content of their own.
			$link    = $child->redirect()->toBool() ? null : static::indexLink($child);
			$subTree = static::indexChildren($child, $level + 1);

			// Keep a page’s own link in the same list as its children
			// instead of separating both with a blank line.
			$body = Str::startsWith(trim($subTree), '- ') === true
				? static::join([$link, $subTree], PHP_EOL)
				: static::join([$link, $subTree]);

			$blocks[] = static::join([
				str_repeat('#', $level) . ' ' . $child->title(),
				$body,
			]);
		}

		if ($links !== []) {
			$blocks[] = static::join($links, PHP_EOL);
		}

		return static::join($blocks);
	}

	/**
	 * `- [Title](url.md): description`
	 */
	private static function indexLink(Page $page): string
	{
		$link = '- [' . static::text($page->title()) . '](' . static::url($page) . ')';

		if ($description = static::description($page)) {
			$link .= ': ' . $description;
		}

		return $link;
	}

	/**
	 * Renders one top level documentation section (Guide, Reference,
	 * Cookbook) including all of its descendants.
	 */
	private static function indexSection(Page $section): string
	{
		return static::join([
			'## ' . $section->title(),
			static::indexChildren($section, 3),
		]);
	}

	private static function inlineCode(mixed $string): string
	{
		$string = trim(static::clean((string)$string));

		return '`' . ($string !== '' ? $string : '–') . '`';
	}

	/**
	 * Whether the page belongs to the documentation.
	 */
	public static function isDocPage(Page $page): bool
	{
		return in_array(
			A::first(Str::split($page->id(), '/')),
			static::SECTIONS,
			true
		);
	}


	/*
	|--------------------------------------------------------------------
	| Markdown helpers
	|--------------------------------------------------------------------
	 */

	/**
	 * Joins blocks, dropping the empty ones. Blocks are separated by a
	 * blank line, which keeps every Markdown parser happy.
	 */
	private static function join(array $blocks, string $separator = PHP_EOL . PHP_EOL): string
	{
		$blocks = A::map($blocks, fn ($block) => trim((string)$block));
		$blocks = array_filter($blocks, fn (string $block) => $block !== '');

		return implode($separator, $blocks);
	}

	/**
	 * Rewrites relative Markdown links to absolute urls. Links that
	 * point at another documentation page get `.md` appended so an
	 * agent following them keeps receiving Markdown.
	 */
	private static function links(string $text): string
	{
		return preg_replace_callback(
			'!\]\((/[^)\s]*)\)!',
			fn (array $matches): string => '](' . static::resolveUrl($matches[1]) . ')',
			$text
		);
	}

	/*
	|--------------------------------------------------------------------
	| KirbyText to Markdown
	|--------------------------------------------------------------------
	 */

	/**
	 * Converts a KirbyText field value to plain Markdown.
	 *
	 * The documentation is authored in Markdown already, so this only
	 * has to resolve the things that are not: KirbyTags, the
	 * `<callout>`/`<code>` boxes of our Markdown parser and internal
	 * links, which are rewritten to point at `.md` pages again.
	 *
	 * @see /site/plugins/site/src/Markdown.php
	 */
	private static function markdown(string|null $text): string|null
	{
		if (empty($text) === true) {
			return null;
		}

		$text = static::clean($text);
		$text = static::codeBoxes($text);
		$text = static::callouts($text);
		$text = static::tags($text);
		$text = static::links($text);

		return trim($text);
	}

	/**
	 * The installed Merx version, used to date the documentation.
	 */
	private static function merxVersion(): string|null
	{
		$version = kirby()->plugin('wagnerwagner/merx')?->version();

		return empty($version) ? null : $version;
	}

	/**
	 * Source file and guide links, rendered as a list so they stay
	 * readable but cannot be mistaken for prose.
	 */
	private static function meta(ReferencePageAbstract $page): string|null
	{
		$meta = [static::guide($page)];

		if ($gitHubUrl = $page->gitHubUrl()) {
			$meta[] = '- Source: `' . $page->relativeFilePath()
				. ($page->line() ? '#L' . $page->line() : '') . '` <' . $gitHubUrl . '>';
		}

		if ($page->isInherited()) {
			$meta[] = '- Inherited from Kirby, not part of the Merx API. '
				. 'See <https://getkirby.com/docs/reference>.';
		}

		return static::join($meta, PHP_EOL);
	}

	private static function option(Page $page): string
	{
		$blocks = [];

		if ($default = $page->defaultValue()) {
			$blocks[] = static::join(['## Default value', static::code($default, 'php')]);
		}

		$blocks[] = static::join(['## Type', static::inlineCode((string)$page->type())]);

		return static::join($blocks);
	}


	/*
	|--------------------------------------------------------------------
	| Page representation
	|--------------------------------------------------------------------
	 */

	/**
	 * Renders a documentation page as Markdown.
	 */
	public static function page(Page $page): string
	{
		return static::join([
			'# ' . static::text($page->headline()->or($page->title())),
			// Everything reflection knows about the page.
			$page instanceof ReferencePageAbstract ? static::reference($page) : null,
			// Hand written intro and text. Reference leaf pages have
			// none, but the reference index pages do.
			static::prose($page),
			static::children($page),
		]) . PHP_EOL;
	}

	private static function params(array $params): string
	{
		$hasDescription = count(array_filter(
			$params,
			fn ($param) => !empty($param['description'])
		)) > 0;

		$head = ['Name', 'Type', 'Default'];
		$rows = [];

		if ($hasDescription === true) {
			$head[] = 'Description';
		}

		foreach ($params as $param) {
			$row = [
				static::inlineCode($param['name']),
				static::inlineCode((string)$param['types']),
				static::inlineCode($param['defaultValue'] ?? '–'),
			];

			if ($hasDescription === true) {
				$row[] = static::text($param['description'] ?? '');
			}

			$rows[] = $row;
		}

		return static::join(['## Parameters', static::table($head, $rows)]);
	}

	private static function parentClass(ReferencePageAbstract $page): string
	{
		$class = static::inlineCode((string)$page->class());

		if (
			method_exists($page, 'declaringClass') &&
			((string)$page->declaringClass()) !== ((string)$page->class())
		) {
			$class .= ' inherited from ' . static::inlineCode((string)$page->declaringClass());
		}

		return static::join(['## Parent class', $class]);
	}

	/**
	 * The `# Title`, `> summary` and agent instructions on top of
	 * `llms.txt`, edited in the Panel on the home page. It is the only
	 * place where we can tell an agent things it cannot infer from the
	 * reference itself, so it is content, not code.
	 *
	 * `{{ version }}` is replaced with the installed Merx version.
	 */
	private static function preamble(): string
	{
		$preamble = static::markdown(Str::template(
			site()->homePage()?->preamble()->value(),
			['version' => static::merxVersion() ?? '2'],
			// Limit the placeholder syntax to `{{ … }}`. Kirby also
			// accepts single braces by default, which would swallow
			// braces in code examples.
			['start' => '\{\{', 'end' => '\}\}']
		));

		// llms.txt needs an H1, so never return an empty preamble.
		return $preamble ?? '# ' . static::text(site()->title());
	}

	/**
	 * The hand written part of a page: intro plus KirbyText.
	 *
	 * Kirby’s `permalinksToUrls()` field method is deliberately not
	 * used here. It parses the value as html, which escapes `>` inside
	 * fenced code blocks. Permalinks are resolved in `resolveUrl()`
	 * instead, where they are still plain text.
	 */
	private static function prose(Page $page): string
	{
		return static::join([
			static::quote(static::text($page->intro())),
			static::markdown($page->text()->value()),
		]);
	}

	private static function quote(string|null $string): string|null
	{
		return static::blockquote($string);
	}

	/**
	 * Reference pages, in the same order as the HTML representation.
	 *
	 * @see /site/snippets/o-reference.php
	 */
	private static function reference(ReferencePageAbstract $page): string
	{
		$blocks = [];

		if (is_string($page->summary())) {
			$blocks[] = static::quote(static::text($page->summary()));
		}

		$blocks[] = static::meta($page);

		if (is_string($page->call())) {
			$blocks[] = static::code($page->call(), 'php');
		}

		if (is_array($page->params()) && count($page->params()) > 0) {
			$blocks[] = static::params($page->params());
		}

		if ($page instanceof \ReferenceTranslationPage) {
			$blocks[] = static::translationStrings($page);
		}

		if ($page instanceof \ReferenceOptionPage) {
			$blocks[] = static::option($page);
		}

		$returnTypes = $page->returnTypes();

		if ($returnTypes instanceof Types && $returnTypes->count() > 0) {
			$blocks[] = static::returnType($page, $returnTypes);
		}

		$exceptions = $page->exceptions();

		if (is_array($exceptions) && count($exceptions) > 0) {
			$blocks[] = static::exceptions($exceptions);
		}

		if (is_string($page->description())) {
			$blocks[] = static::join([
				'## Description',
				static::markdown($page->description()),
			]);
		}

		if ($page->class() instanceof Type) {
			$blocks[] = static::parentClass($page);
		}

		if ($page instanceof \ReferenceApiModelPage) {
			$blocks[] = static::apiModel($page);
		}

		if ($page instanceof \ReferenceBlueprintPage) {
			$blocks[] = static::join([
				'## Default blueprint',
				static::code($page->blueprintFileContent(), 'yaml'),
			]);
		}

		if ($page->examples()->isNotEmpty()) {
			$blocks[] = static::join([
				'## Examples',
				static::markdown($page->examples()->value()),
			]);
		}

		if ($page instanceof \ReferenceApiRoutePage) {
			$blocks[] = static::apiRoute($page);
		}

		return static::join($blocks);
	}

	/**
	 * Turns a link target into an absolute url, pointing at the `.md`
	 * representation whenever the target is a documentation page.
	 */
	private static function resolveUrl(string $target): string
	{
		// Panel links are stored as permalinks.
		if (Str::startsWith($target, 'page://') === true || Str::startsWith($target, 'file://') === true) {
			try {
				$target = Uuid::for($target)?->model()?->url() ?? $target;
			} catch (InvalidArgumentException) {
				// Not a permalink after all, keep it as it is.
			}
		}

		// Leave external links, mailto and anchors alone.
		if (
			Str::contains($target, '://') === true ||
			Str::startsWith($target, 'mailto:') === true ||
			Str::startsWith($target, '#') === true
		) {
			return $target;
		}

		$id   = trim(Str::before($target, '#') ?: $target, '/');
		$hash = Str::contains($target, '#') === true ? '#' . Str::after($target, '#') : '';

		if ($id !== '' && $page = static::find($id)) {
			return static::url($page) . $hash;
		}

		return url($target);
	}

	private static function returnType(ReferencePageAbstract $page, Types $returnTypes): string
	{
		$blocks = [];

		if (
			$page instanceof \ReferenceApiRoutePage &&
			($returnType = $page->returnType()) &&
			$returnType->getDataType() === 'class'
		) {
			$blocks[] = static::join(['## API model', static::inlineCode((string)$returnType)]);
		}

		$blocks[] = static::join([
			'## Return type',
			static::inlineCode((string)$returnTypes),
			$page->returnDescription() ? static::text($page->returnDescription()) : null,
		]);

		return static::join($blocks);
	}

	/**
	 * Renders a Markdown table. Cells are forced onto a single line and
	 * pipes are escaped, otherwise union types like `int|null` would
	 * break the table.
	 */
	private static function table(array $head, array $rows): string
	{
		$cell = fn (mixed $cell): string => str_replace(
			['|', PHP_EOL],
			['\|', ' '],
			static::clean(trim((string)$cell))
		);

		$line = fn (array $cells): string => '| '
			. implode(' | ', A::map($cells, $cell)) . ' |';

		return static::join([
			$line($head),
			'|' . str_repeat(' --- |', count($head)),
			...A::map($rows, $line),
		], PHP_EOL);
	}

	/**
	 * Resolves the KirbyTags used in the documentation. Kirby would
	 * render these to HTML, which is exactly what we do not want here.
	 */
	private static function tags(string $text): string
	{
		// (link: /guide/… text: Label)
		$text = preg_replace_callback(
			'!\(link:\s*([^\s)]+)(?:\s+text:\s*([^)]*))?\)!',
			function (array $matches): string {
				$url  = static::resolveUrl($matches[1]);
				$text = trim($matches[2] ?? '');
				return '[' . ($text !== '' ? $text : $url) . '](' . $url . ')';
			},
			$text
		);

		// (email: merx@wagnerwagner.de)
		$text = preg_replace_callback(
			'!\(email:\s*([^\s)]+)(?:\s+text:\s*([^)]*))?\)!',
			function (array $matches): string {
				$text = trim($matches[2] ?? '');
				return $text !== ''
					? '[' . $text . '](mailto:' . $matches[1] . ')'
					: '<' . $matches[1] . '>';
			},
			$text
		);

		// (file: …) and (image: …) have no meaning without the page
		// context, so link the file instead of embedding it.
		return preg_replace_callback(
			'!\((?:file|image):\s*([^\s)]+)[^)]*\)!',
			fn (array $matches): string => '[' . $matches[1] . '](' . static::resolveUrl($matches[1]) . ')',
			$text
		);
	}

	/**
	 * Normalizes a value for use in single line contexts: headlines,
	 * descriptions and table cells.
	 */
	private static function text(mixed $string): string
	{
		$string = static::clean((string)$string);
		$string = strip_tags($string);

		return trim(html_entity_decode($string, ENT_QUOTES, 'UTF-8'));
	}

	private static function translationStrings(Page $page): string
	{
		$rows = [];

		foreach ($page->translationStrings() as $language => $string) {
			$rows[] = [$language, static::text($string)];
		}

		return static::join([
			'## Translation strings',
			static::table(['Language', 'Translation'], $rows),
		]);
	}

	/**
	 * Returns the `.md` url of a documentation page.
	 */
	public static function url(Page $page): string
	{
		return $page->url() . '.md';
	}
}
