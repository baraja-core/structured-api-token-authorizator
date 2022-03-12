<?php

declare(strict_types=1);

namespace Baraja\TokenAuthorizator;


/**
 * A service for verifying privileges to serve the current API request.
 *
 * This interface is used to check the permissions of an API request call in the baraja-core/structured-api library.
 * The user request is authenticated by passing the "token" parameter with any HTTP method.
 *
 * This service implements the internal logic for authentication. A particular application may authenticate access in different ways.
 */
interface VerificationStrategy
{
	/**
	 * Checks if given token is valid for current request.
	 * If so, an API response will be returned. If no, access is denied.
	 */
	public function verify(string $token): bool;

	/**
	 * Verifies that the currently selected token authentication strategy is active.
	 * If not, all requests will be automatically rejected.
	 * Use this setting, for example, when you want to register your strategy
	 * but the user has not yet set up a token for authentication.
	 */
	public function isActive(): bool;
}
