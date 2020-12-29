<?php

declare(strict_types=1);

namespace Baraja\TokenAuthorizator;


final class SimpleStrategy implements VerificationStrategy
{
	private ?string $token;


	public function __construct(?string $token)
	{
		$this->token = $token;
	}


	public function verify(string $token): bool
	{
		return $token === $this->token;
	}


	public function isActive(): bool
	{
		return $this->token !== null;
	}
}
