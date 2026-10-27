<?php

use Kirby\Content\Field;
use Wagnerwagner\Site\ApiExamples;
use Wagnerwagner\Site\Type;
use Wagnerwagner\Site\Types;

class ReferenceApiRoutePage extends \Wagnerwagner\Site\ReferencePageAbstract
{
	public function apiModelBaseUrl(): string
	{
		return url($this->parent()->parent()->id() . '/models/');
	}

	public function call(): string
	{
		$pattern = $this->pattern();
		$method = $this->method();

		$call = "fetch('/api/$pattern', {\n";
		$call .= "	method: '$method',\n";

		if ($body = $this->exampleRequestBody()) {
			$json = ApiExamples::json($body);
			// Indent with tabs, one level deeper than `body`
			$json = preg_replace_callback('/^(?: {4})+/m', fn ($m) => str_repeat("\t", strlen($m[0]) / 4), $json);
			$json = str_replace("\n", "\n\t", $json);

			$call .= "	headers: {\n";
			$call .= "		'Accept': 'application/json',\n";
			$call .= "		'Content-Type': 'application/json',\n";
			$call .= "	},\n";
			$call .= "	body: JSON.stringify($json),\n";
		}

		$call .= '});';

		return $call;
	}

	/**
	 * Request body of the example, or null when the route takes none
	 */
	public function exampleRequestBody(): array|null
	{
		return ApiExamples::requestBody($this->pattern(), $this->method());
	}

	/**
	 * Response of the route, called with the example request against the
	 * fixture shop, or null when the route has no example
	 */
	public function exampleResponse(): array|null
	{
		return ApiExamples::request($this->pattern(), $this->method());
	}

	/**
	 * Failing example requests against the fixture shop, with their
	 * error responses
	 *
	 * @return array<string, array{body: array, cart: bool, response: array}> By title
	 */
	public function exampleErrors(): array
	{
		return ApiExamples::errors($this->pattern(), $this->method());
	}

	public function exceptions(): array
	{
		return [];
	}

	public function params(): array
	{
		return $this->reflection()->getParameters();
	}

	public function reflection(): Reflector|null
	{
		return $this->content()->reflection()->value();
	}

	public function returnType(): Type|null
	{
		return $this->returnTypes()->first();
	}

	public function returnTypes(): Types|null
	{
		if (!$this->reflection()->hasReturnType()) {
			return new Types(['void']);
		}
		return new Types([$this->reflection()->getReturnType()]);
	}

	public function title(): Field
	{
		$pattern = $this->pattern();
		$method = $this->method();
		return parent::title()->value($method . ' ' . $pattern);
	}
}
