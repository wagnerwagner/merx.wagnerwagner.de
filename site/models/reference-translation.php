<?php

use Wagnerwagner\Site\ReferencePageAbstract;

class ReferenceTranslationPage extends ReferencePageAbstract
{
	/**
	 * The translation table below lists the string in every language, so
	 * repeating the English one above it would say the same thing twice. It
	 * stays the page’s description in `llms.txt`, where there is no table.
	 */
	public function summary(): ?string
	{
		return null;
	}

	public function translationStrings(): array
	{
		$translationKey = $this->key()->value();
		$translations = $this->kirby()->plugin('wagnerwagner/merx')->extends()['translations'];
		$languages = array_keys($translations);
		$translationStrings = [];
		foreach ($languages as $langKey) {
			if (isset($translations[$langKey][$translationKey])) {
				$translationStrings[$langKey] = $translations[$langKey][$translationKey];
			}
		}
		// Sort so 'en' is always first, then retain other languages in their current order
		if (isset($translationStrings['en'])) {
			$en = ['en' => $translationStrings['en']];
			unset($translationStrings['en']);
			$translationStrings = [...$en, ...$translationStrings];
		}
		return $translationStrings;
	}
}
