<?php

declare(strict_types=1);

use Illuminate\Config\Repository;
use Illuminate\Container\Container;
use Illuminate\Support\Facades\Facade;
use PHPUnit\Framework\TestCase;

final class ApolloServiceProviderTest extends TestCase
{
    public function testApolloClassesExistAndProviderIsDiscoverable(): void
    {
        self::assertTrue(class_exists(\Ometra\Apollo\Sdk\Apollo::class));
        self::assertTrue(class_exists(\Ometra\Apollo\Sdk\Providers\ApolloServiceProvider::class));
        self::assertTrue(class_exists(\Ometra\Apollo\Sdk\Facades\Apollo::class));
    }

    public function testApolloConfigUsesExactModuleBaseUrlKeys(): void
    {
        $config = require __DIR__ . '/../../config/apollo.php';

        self::assertArrayHasKey('modules', $config);
        self::assertSame('PROTEUS_BASE_URL', $config['modules']['proteus']['base_url_env']);
        self::assertSame('PULSE_BASE_URL', $config['modules']['pulse']['base_url_env']);
        self::assertSame('FLARE_BASE_URL', $config['modules']['flare']['base_url_env']);
        self::assertSame('IGNIS_BASE_URL', $config['modules']['ignis']['base_url_env']);
    }

    public function testApolloConfigDoesNotDefineCaronteAuthConfiguration(): void
    {
        $config = require __DIR__ . '/../../config/apollo.php';

        self::assertArrayNotHasKey('auth', $config);
        self::assertStringNotContainsString('CARONTE_', file_get_contents(__DIR__ . '/../../config/apollo.php'));
    }

    public function testFacadeResolvesApolloSingletonFromContainer(): void
    {
        $app = new Container();
        Container::setInstance($app);
        Facade::clearResolvedInstances();
        Facade::setFacadeApplication($app);

        $app->instance('config', new Repository(['apollo' => require __DIR__ . '/../../config/apollo.php']));

        $provider = new \Ometra\Apollo\Sdk\Providers\ApolloServiceProvider($app);
        $provider->register();

        self::assertInstanceOf(\Ometra\Apollo\Sdk\Apollo::class, \Ometra\Apollo\Sdk\Facades\Apollo::getFacadeRoot());

        Facade::clearResolvedInstances();
        Facade::setFacadeApplication(null);
        Container::setInstance(null);
    }
}
