Structured API token authorizator
=================================

![Integrity check](https://github.com/baraja-core/structured-api-token-authorizator/workflows/Integrity%20check/badge.svg)

A simple token authorizer for authenticating HTTP requests.

This package is the official extension for the [Baraja Structured API](https://github.com/baraja-core/structured-api).

Simple usage
------------

Install this package using Composer and register the DIC extension (if you use [Baraja Package manager](https://github.com/baraja-core/package-manager), it will be registered automatically).

Extension definition for manual usage:

```yaml
extensions:
   tokenAuthorizator: Baraja\TokenAuthorizator\TokenAuthorizatorExtension
```

The package automatically disables the default system method of authenticating requests through Nette User and will require token authentication.

A token is any valid string in the query parameter `token`, or in BODY (in the case of a POST request). The token evaluates as an endpoint call parameter and can be passed to the target endpoint as a string.

Request verification
--------------------

If you are not using your own token authentication implementation, the default `SimpleStrategy` will be used, which you can configure the token via NEON configuration.

If you do not set a token, all requests (even without a token) will be considered valid.

Simple configuration example:

```yaml
tokenAuthorizator:
   token: abcd
```

This configuration accepts requests as: `/api/v1/user?token=abcd`.

Custom authentication
---------------------

If you need more complex authentication logic, implement a service that implements the `VerificationStrategy` interface and register it with the DIC. This service will be called automatically when all requests are verified.

📄 License
-----------

`baraja-core/structured-api-token-authorizator` is licensed under the MIT license. See the [LICENSE](https://github.com/baraja-core/structured-api-token-authorizator/blob/master/LICENSE) file for more details.
