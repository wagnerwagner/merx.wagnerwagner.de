<?php

namespace Wagnerwagner\Site;

use DomDocument;
use Kirby\Toolkit\Str;
use ParsedownExtra;

class Markdown extends ParsedownExtra
{
	protected $lastH2;

	/**
	 * Extend Parsedown's constructor
	 */
	public function __construct(protected array $options = [])
	{
		parent::__construct();

		// register the new box type
		$this->BlockTypes['<'] = ['Box'] + $this->BlockTypes['<'];

		$this->setBreaksEnabled(true);
	}

	protected function parseAttributes(string $input): array
	{
		$dom = new DomDocument();
		$dom->loadHtml('<html ' . $input . '/>');
		$attributes = [];
		foreach ($dom->documentElement->attributes as $name => $attr) {
			$attributes[$name] = $attr->value;
		}
		return $attributes;
	}

	/**
	 * Parse Kirby's text boxes
	 */
	protected function blockBox(array $Line, null|array $Block = null)
	{
		if (preg_match('!<(callout|code)(.*?)>!', $Line['text'], $matches)) {

			$type  = strtolower($matches[1]);
			$Block = [
				'box' => [
					'type' => $type,
					'html' => null,
					'attrs' => $this->parseAttributes($matches[2])
				],
			];

			// if the block is just across a single line
			if (preg_match('!</' . $type . '>$!', $Line['text'])) {
				$html = $Line['body'];
				$html = preg_replace('!^<' . $type . '.*?>(.*)</' . $type . '>$!', '$1', $html);

				$Block['complete'] = true;
				$Block['box']['html'] = $html;
			}

			return $Block;
		}

		return null;
	}

	/**
	 * Add lines to the boxes
	 */
	protected function blockBoxContinue(array $Line, array $Block)
	{
		if ($Block['complete'] ?? false) {
			return;
		}

		if ($Line['text'] === '</' . $Block['box']['type'] . '>') {
			$Block['complete'] = true;
			return $Block;
		}

		if (isset($Block['interrupted'])) {
			$Block['box']['html'] .= str_repeat("\n", $Block['interrupted']);
			unset($Block['interrupted']);
		}

		$Block['box']['html'] .= "\n" . $Line['body'];

		return $Block;
	}

	/**
	 * Finalize the boxes
	 */
	protected function blockBoxComplete($Block)
	{
		$html = $Block['box']['html'] ?? '';
		$html = kt($html);
		$theme = $Block['box']['attrs']['theme'] ?? null;
		$caption = $Block['box']['attrs']['caption'] ?? null;

		$moleculeName = 'm-' . $Block['box']['type'];

		$html = snippet($moleculeName, [
			'theme' => $theme,
			'caption' => $caption,
			'html' => $html,
		], true);

		return [
			'element' => [
				'rawHtml' => $html,
			]
		];
	}

	protected function blockHeader($Line)
	{
		$Block = parent::blockHeader($Line);

		if (!$Block) {
			return;
		}

		$slug  = Str::slug(Str::unhtml($this->text($Block['element']['handler']['argument'])));
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
					'id' => $slug
				],
				'element' => [
					'name' => 'a',
					'handler' => $Block['element']['handler'],
					'nonNestables' => ['Url', 'Link'],
					'attributes' => [
						'href' => '#' . $slug
					]
				]
			]
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

	protected function blockFencedCodeComplete($Block)
	{
		$Block = [
			'element' => [
				'name' => 'div',
				'attributes' => [
					'class' => 'm-code',
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
						'destination' => 'elements'
					]
				],
			];
		}
	}

	protected function inlineCodeHandler($text)
	{
		return [
			[
				'rawHtml' => (new Types($text))->toHtml()
			]
		];
	}
}
