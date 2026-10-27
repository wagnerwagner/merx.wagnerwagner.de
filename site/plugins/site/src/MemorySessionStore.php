<?php

namespace Wagnerwagner\Site;

use Kirby\Exception\NotFoundException;
use Kirby\Session\Store;

/**
 * Session store that keeps sessions in memory for the current request only
 *
 * Used by `ApiExamples`, so the example cart never ends up in the session
 * of the visitor who reads the documentation.
 */
class MemorySessionStore extends Store
{
	/** @var array<string, string> */
	protected array $sessions = [];

	public function collectGarbage(): void
	{
		$this->sessions = [];
	}

	public function createId(int $expiryTime): string
	{
		$id = static::generateId();
		$this->sessions[$expiryTime . '.' . $id] = '';
		return $id;
	}

	public function destroy(int $expiryTime, string $id): void
	{
		unset($this->sessions[$expiryTime . '.' . $id]);
	}

	public function exists(int $expiryTime, string $id): bool
	{
		return array_key_exists($expiryTime . '.' . $id, $this->sessions);
	}

	public function get(int $expiryTime, string $id): string
	{
		return $this->sessions[$expiryTime . '.' . $id] ?? throw new NotFoundException(
			key: 'session.filestore.notFound',
			data: ['name' => $expiryTime . '.' . $id],
			translate: false,
		);
	}

	public function lock(int $expiryTime, string $id): void
	{
	}

	public function set(int $expiryTime, string $id, string $data): void
	{
		$this->sessions[$expiryTime . '.' . $id] = $data;
	}

	public function unlock(int $expiryTime, string $id): void
	{
	}
}
