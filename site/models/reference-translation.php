<?php

use Wagnerwagner\Site\ReferencePageAbstract;

class ReferenceTranslationPage extends ReferencePageAbstract
{
	public function translationStrings(): array
	{
		$translationKey = $this->key()->value();
		$translations = $this->kirby()->plugin('ww/merx')->extends()['translations'];
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
