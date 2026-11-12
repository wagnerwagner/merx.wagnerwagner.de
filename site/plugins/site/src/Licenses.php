<?php

namespace Wagnerwagner\Site;

use Base32\Base32;
use Kirby\Filesystem\F;
use RuntimeException;
use Throwable;
use Wagnerwagner\Merx\License;

/**
 * Issues the Merx license keys
 *
 * A key is derived from its number and `wagnerwagner.site.licenseSecret`, so
 * all there is to store is how many keys have been issued. Merx cannot know
 * the secret, its source is public. It ships the fingerprints of the keys
 * instead, in the file `composer license-keys` writes, and accepts no other key.
 */
class Licenses
{
	/**
	 * Whether the next keys can be sold: there is a secret, and Merx knows
	 * the keys made from it
	 */
	public static function available(int $quantity): bool
	{
		try {
			$number = (int)F::read(static::counter()) + max($quantity, 1);

			// The keys are issued in order, so the last one stands for all of them
			return in_array(License::fingerprint(static::key($number)), str_split((string)F::read(static::file()), 10), true);
		} catch (Throwable) {
			return false;
		}
	}

	/**
	 * Counts the issued keys. Kirby ignores files with a leading dot.
	 */
	protected static function counter(): string
	{
		return site()->ordersPage()->root() . '/.licenses';
	}

	/**
	 * The list of fingerprints Merx ships
	 */
	public static function file(): string
	{
		return kirby()->plugin('wagnerwagner/merx')->root() . '/config/licenses.bin';
	}

	/**
	 * Fingerprints of the first keys, the way Merx reads them
	 *
	 * Sorted, so that the place of a fingerprint does not tell the number of
	 * its key, which is how many licenses had been sold by then.
	 */
	public static function fingerprints(int $count): string
	{
		$fingerprints = [];

		for ($number = 1; $number <= $count; $number++) {
			$fingerprints[] = License::fingerprint(static::key($number));
		}

		sort($fingerprints);

		return implode('', $fingerprints);
	}

	/**
	 * Takes the next keys
	 *
	 * @return string[]
	 */
	public static function issue(int $quantity): array
	{
		if ($quantity < 1) {
			return [];
		}

		// The counter is the lock as well, which keeps two orders completing
		// at the same moment from getting the same keys
		$handle = fopen(static::counter(), 'c+');

		if ($handle === false || flock($handle, LOCK_EX) === false) {
			throw new RuntimeException('Could not lock ' . static::counter());
		}

		try {
			$issued = (int)stream_get_contents($handle);
			$keys = array_map(static::key(...), range($issued + 1, $issued + $quantity));

			ftruncate($handle, 0);
			rewind($handle);
			fwrite($handle, (string)($issued + $quantity));

			return $keys;
		} finally {
			flock($handle, LOCK_UN);
			fclose($handle);
		}
	}

	/**
	 * The license key with the given number, starting at 1
	 */
	public static function key(int $number): string
	{
		$secret = option('wagnerwagner.site.licenseSecret');

		if (is_string($secret) === false || strlen($secret) < 32) {
			throw new RuntimeException('The option wagnerwagner.site.licenseSecret is missing or shorter than 32 characters');
		}

		// 80 bits, as 16 characters
		$key = Base32::encode(substr(hash_hmac('sha256', (string)$number, $secret, true), 0, 10));

		return 'MERX2-' . substr($key, 0, 8) . '-' . substr($key, 8);
	}
}
