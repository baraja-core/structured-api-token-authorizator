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
		if ($this->strategy->isActive() === false) {
			return null;
		}

		$requireToken = false;
		try {
			$ref = new \ReflectionClass($endpoint);
			$docComment = trim((string) $ref->getDocComment());
			if (preg_match('/@public(?:$|\s|\n)/', $docComment) === 1) {
				return null;
			}
			foreach ($ref->getAttributes(PublicEndpoint::class) as $publicEndpointAttribute) {
				bdump($publicEndpointAttribute->getArguments());
				if (($publicEndpointAttribute->getArguments()['requireToken'] ?? false) === true) {
					$requireToken = true;
				}
			}
		} catch (\ReflectionException $e) {
			throw new \InvalidArgumentException(
				sprintf('Endpoint "%s" can not be reflected: %s', $endpoint::class, $e->getMessage()),
				500,
				$e,
			);
		}

		$token = $params['token'] ?? null;
		if ($token === null && $requireToken === true) {
			throw new \InvalidArgumentException('Parameter "token" is required.');
		}
		if (is_string($token) === false && $requireToken === true) {
			throw new \InvalidArgumentException(sprintf('Parameter "token" must be string, but type "%s" given.', get_debug_type($token)));
		}

		if ($requireToken === false || $this->strategy->verify($token)) {
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
}
