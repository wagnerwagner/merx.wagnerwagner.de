<?php

namespace Wagnerwagner\Site;

use Kirby\Toolkit\Str;
use ParsedownExtra;

class Markdown extends ParsedownExtra
{
	protected $lastH2;

	/**
	 * Allow `key="value"` next to `#id` and `.class` in `{…}`:
	 * [Source code](https://github.com/…){.a-button data-variant="outline"}
	 */
	protected $regexAttribute = '(?:(?:[#.][-\w]+|[-\w]+="[^"]*")[ ]*)';

	/**
	 * Extend Parsedown's constructor
	 */
	public function __construct(protected array $options = [])
	{
		parent::__construct();

		$this->setBreaksEnabled(true);
	}

	/**
	 * Read the file path from the info string: ```php title="/site/config/config.php"
	 */
	protected function blockFencedCode($Line)
	{
		$Block = parent::blockFencedCode($Line);

		if ($Block !== null && preg_match('!\stitle="([^"]*)"!', $Line['text'], $matches)) {
			$Block['title'] = $matches[1];
		}

		return $Block;
	}

	protected function blockFencedCodeComplete($Block)
	{
		$Element = [
			'name' => 'div',
			'attributes' => [
				'class' => 'm-code',
			],
			'elements' => [$Block['element']],
		];

		if (isset($Block['title']) === false) {
			return ['element' => $Element];
		}

		return [
			'element' => [
				'rawHtml' => snippet('m-code', [
					'caption' => esc($Block['title']),
					'html' => $this->element($Element),
				], true),
			],
		];
	}

	protected function blockHeader($Line)
	{
		$Block = parent::blockHeader($Line);

		if (!$Block) {
			return;
		}

		$slug = Str::slug(Str::unhtml($this->text($Block['element']['handler']['argument'])));
		$level = $Block['element']['name'];

		switch ($level) {
			case 'h2':
				$this->lastH2 = $slug;
				break;
			case 'h3':
				$slug = $this->lastH2 . '__' . $slug;
				break;
			default:
				return $Block;
				break;
		}

		if (isset($this->options['idPrefix'])) {
			$slug = $this->options['idPrefix'] . '__' . $slug;
		}

		return [
			'element' => [
				'name' => $level,
				'attributes' => [
					'id' => $slug,
				],
				'element' => [
					'name' => 'a',
					'handler' => $Block['element']['handler'],
					'nonNestables' => ['Url', 'Link'],
					'attributes' => [
						'href' => '#' . $slug,
					],
				],
			],
		];
	}

	/**
	 * Render GitHub alerts (`> [!NOTE]`) as callout boxes
	 *
	 * @see https://docs.github.com/en/get-started/writing-on-github/getting-started-with-writing-and-formatting-on-github/basic-writing-and-formatting-syntax#alerts
	 */
	protected function blockQuoteComplete($Block)
	{
		$lines = $Block['element']['handler']['argument'];

		if (!preg_match('!^\[\!(NOTE|TIP|IMPORTANT|WARNING|CAUTION)\]\s*$!', $lines[0], $matches)) {
			return $Block;
		}

		$html = snippet('m-callout', [
			'theme' => strtolower($matches[1]),
			'html' => $this->elements($this->linesElements(array_slice($lines, 1))),
		], true);

		return [
			'element' => [
				'rawHtml' => $html,
			],
		];
	}

	/**
	 * Wrap tables in a div
	 */
	protected function blockTableComplete($Block)
	{
		$Block = [
			'element' => [
				'name' => 'div',
				'attributes' => [
					'class' => 'm-table',
				],
				'elements' => [$Block['element']],
			],
		];

		return $Block;
	}

	/**
	 * Highlight data types in inline code
	 */
	protected function inlineCode($Excerpt)
	{
		$Excerpt = parent::inlineCode($Excerpt);

		if ($Excerpt !== null) {
			return [
				...$Excerpt,
				'element' => [
					'handler' => [
						'function' => 'inlineCodeHandler',
						'argument' => $Excerpt['element']['text'],
						'destination' => 'elements',
					],
				],
			];
		}
	}

	protected function inlineCodeHandler($text)
	{
		return [
			[
				'rawHtml' => (new Types($text))->toHtml(),
			],
		];
	}

	/**
	 * Parse `#id`, `.class` and `key="value"`
	 *
	 * @see $regexAttribute
	 */
	protected function parseAttributeData($attributeString)
	{
		preg_match_all('!([#.])([-\w]+)|([-\w]+)="([^"]*)"!', $attributeString, $matches, PREG_SET_ORDER);

		$Data = [];
		$classes = [];

		foreach ($matches as $match) {
			match ($match[1]) {
				'#' => $Data['id'] = $match[2],
				'.' => $classes[] = $match[2],
				default => $Data[$match[3]] = $match[4],
			};
		}

		if ($classes !== []) {
			$Data['class'] = implode(' ', $classes);
		}

		return $Data;
	}
}
