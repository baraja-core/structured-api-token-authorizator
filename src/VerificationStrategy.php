<?php

declare(strict_types=1);

namespace Baraja\TokenAuthorizator;


interface VerificationStrategy
{
	public function verify(string $token): bool;

	public function isActive(): bool;
}
