<?php

declare(strict_types=1);

namespace Baraja\TokenAuthorizator;


use Baraja\StructuredApi\Attributes\PublicEndpoint;
use Baraja\StructuredApi\Endpoint;
use Baraja\StructuredApi\Middleware\MatchExtension;
use Baraja\StructuredApi\Response;

final class TokenAuthorizator implements MatchExtension
{
	private VerificationStrategy $strategy;


	public function __construct(?VerificationStrategy $strategy = null, ?string $secret = null)
	{
		if ($strategy === null && $secret === null) {
			throw new \LogicException('Please define Verification strategy or secret token in your configuration.');
		}
		$this->strategy = $strategy ?? new SimpleStrategy($secret);
	}


	public function setStrategy(VerificationStrategy $strategy): void
	{
		$this->strategy = $strategy;
	}


	/**
	 * @param array<string|int, mixed> $params
	 */
	public function beforeProcess(Endpoint $endpoint, array $params, string $action, string $method): ?Response
	{
		if (
			$this->strategy->isActive() === false
			|| $this->isPublicAccess($endpoint)
			|| $this->isTokenOk($params['token'] ?? null)
		) {
			return null;
		}

		throw new \InvalidArgumentException('Token is invalid or expired, please contact your administrator.');
	}


	/**
	 * @param array<string|int, mixed> $params
	 */
	public function afterProcess(Endpoint $endpoint, array $params, ?Response $response): ?Response
	{
		return null;
	}


	private function isPublicAccess(Endpoint $endpoint): bool
	{
		$requireToken = false;
		$ref = new \ReflectionClass($endpoint);
		if (str_contains((string) $ref->getDocComment(), '@public')) {
			return true;
		}
		foreach ($ref->getAttributes(PublicEndpoint::class) as $publicEndpointAttribute) {
			if (($publicEndpointAttribute->getArguments()['requireToken'] ?? false) === true) {
				$requireToken = true;
			}
		}

		return $requireToken === false;
	}


	private function isTokenOk(mixed $token): bool
	{
		if ($token === null) {
			throw new \InvalidArgumentException('Parameter "token" is required.');
		}
		if (is_string($token) === false) {
			throw new \InvalidArgumentException(sprintf('Parameter "token" must be string, but type "%s" given.', get_debug_type($token)));
		}

		return $this->strategy->verify($token);
	}
}
