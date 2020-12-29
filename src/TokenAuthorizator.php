<?php

declare(strict_types=1);

namespace Baraja\TokenAuthorizator;


use Baraja\StructuredApi\Endpoint;
use Baraja\StructuredApi\Middleware\MatchExtension;
use Baraja\StructuredApi\Response;

final class TokenAuthorizator implements MatchExtension
{
	private VerificationStrategy $strategy;


	public function __construct(?string $secret, ?VerificationStrategy $strategy = null)
	{
		$this->strategy = $strategy ?? new SimpleStrategy($secret);
	}


	public function setStrategy(VerificationStrategy $strategy): void
	{
		$this->strategy = $strategy;
	}


	/**
	 * @param mixed[] $params
	 */
	public function beforeProcess(Endpoint $endpoint, array $params, string $action, string $method): ?Response
	{
		if ($this->strategy->isActive() === false) {
			return null;
		}
		try {
			$docComment = trim((string) (new \ReflectionClass($endpoint))->getDocComment());
			if (preg_match('/@public(?:$|\s|\n)/', $docComment)) {
				return null;
			}
		} catch (\ReflectionException $e) {
			throw new \InvalidArgumentException('Endpoint "' . \get_class($endpoint) . '" can not be reflected: ' . $e->getMessage(), $e->getCode(), $e);
		}
		if (isset($params['token']) === false) {
			throw new \InvalidArgumentException('Parameter "token" is required.');
		}
		if ($this->strategy->verify($params['token'])) {
			return null;
		}
		throw new \InvalidArgumentException('Token is invalid or expired, please contact your administrator.');
	}


	/**
	 * @param mixed[] $params
	 */
	public function afterProcess(Endpoint $endpoint, array $params, ?Response $response): ?Response
	{
		return null;
	}
}
