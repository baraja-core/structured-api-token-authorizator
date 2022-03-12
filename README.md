Structured API token authorizator
=================================

![Integrity check](https://github.com/baraja-core/structured-api-token-authorizator/workflows/Integrity%20check/badge.svg)

A simple token authorizer for authenticating HTTP requests.

This package is the official extension for the [Baraja Structured API](https://github.com/baraja-core/structured-api).

📦 Installation
---------------

It's best to use [Composer](https://getcomposer.org) for installation, and you can also find the package on
[Packagist](https://packagist.org/packages/baraja-core/structured-api-token-authorizator) and
[GitHub](https://github.com/baraja-core/structured-api-token-authorizator).

To install, simply use the command:

```shell
$ composer require baraja-core/structured-api-token-authorizator
```

You can use the package manually by creating an instance of the internal classes, or register a DIC extension to link the services directly to the Nette Framework.

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

Token verification at the endpoint level
----------------------------------------

Token usage is verified at the endpoint level. By default, all endpoints have access enabled and are governed by the `PublicEndpoint` attribute defined by the baraja-core/structured-api package.

If you want to require token authentication in your endpoint, set the attribute directly above the endpoint definition.

For example:

```php
#[PublicEndpoint(requireToken: true)]
class ArticleEndpoint extends BaseEndpoint
{
}
```

Custom authentication
---------------------

If you need more complex authentication logic, implement a service that implements the `VerificationStrategy` interface and register it with the DIC. This service will be called automatically when all requests are verified.

📄 License
-----------

`baraja-core/structured-api-token-authorizator` is licensed under the MIT license. See the [LICENSE](https://github.com/baraja-core/structured-api-token-authorizator/blob/master/LICENSE) file for more details.
