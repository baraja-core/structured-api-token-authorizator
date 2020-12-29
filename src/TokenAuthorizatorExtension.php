<?php

declare(strict_types=1);

namespace Baraja\TokenAuthorizator;


use Baraja\StructuredApi\ApiExtension;
use Baraja\StructuredApi\ApiManager;
use Baraja\StructuredApi\Entity\Convention;
use Nette\DI\CompilerExtension;
use Nette\DI\Definitions\ServiceDefinition;
use Nette\Schema\Expect;
use Nette\Schema\Schema;

final class TokenAuthorizatorExtension extends CompilerExtension
{
	/**
	 * @return string[]
	 */
	public static function mustBeDefinedBefore(): array
	{
		return [ApiExtension::class];
	}


	public function getConfigSchema(): Schema
	{
		return Expect::structure([
			'token' => Expect::string(),
		])->castTo('array');
	}


	public function beforeCompile(): void
	{
		/** @var mixed[] $config */
		$config = $this->getConfig();
		$builder = $this->getContainerBuilder();

		/** @var ServiceDefinition $apiManager */
		$apiManager = $builder->getDefinitionByType(ApiManager::class);

		/** @var ServiceDefinition $convention */
		$convention = $builder->getDefinitionByType(Convention::class);

		$convention->addSetup('?->setIgnoreDefaultPermission(true)', ['@self']);

		$builder->addDefinition($this->prefix('tokenAuthorizator'))
			->setFactory(TokenAuthorizator::class)
			->setAutowired(TokenAuthorizator::class)
			->setArgument('secret', $config['token'] ?? null);

		$apiManager->addSetup('?->addMatchExtension(?)', ['@self', '@' . TokenAuthorizator::class]);
	}
}
