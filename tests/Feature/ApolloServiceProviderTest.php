<?php

declare(strict_types=1);

use Illuminate\Config\Repository;
use Illuminate\Container\Container;
use Illuminate\Support\Facades\Facade;
use Illuminate\Support\ServiceProvider;
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

    public function testApolloConfigDefinesErrorPagesOptOut(): void
    {
        $config = require __DIR__ . '/../../config/apollo.php';

        self::assertArrayHasKey('error_pages', $config);
        self::assertArrayHasKey('enabled', $config['error_pages']);
    }

    public function testProviderAppendsApolloViewsAsErrorPageFallback(): void
    {
        $app = self::makeApplicationContainer();
        $config = new Repository([
            'apollo' => array_replace_recursive(require __DIR__ . '/../../config/apollo.php', [
                'error_pages' => ['enabled' => true],
                'ignis_groups' => ['enabled' => false],
            ]),
            'view' => [
                'paths' => ['/host/resources/views'],
            ],
        ]);

        $app->instance('config', $config);
        Container::setInstance($app);

        $provider = new \Ometra\Apollo\Sdk\Providers\ApolloServiceProvider($app);
        $provider->boot();

        $paths = $config->get('view.paths');

        self::assertSame('/host/resources/views', $paths[0]);
        self::assertSame((string) realpath(dirname(__DIR__, 2) . '/resources/views'), $paths[1]);

        Container::setInstance(null);
    }

    public function testProviderDoesNotAppendErrorPageFallbackWhenDisabled(): void
    {
        $app = self::makeApplicationContainer();
        $config = new Repository([
            'apollo' => array_replace_recursive(require __DIR__ . '/../../config/apollo.php', [
                'error_pages' => ['enabled' => false],
                'ignis_groups' => ['enabled' => false],
            ]),
            'view' => [
                'paths' => ['/host/resources/views'],
            ],
        ]);

        $app->instance('config', $config);
        Container::setInstance($app);

        $provider = new \Ometra\Apollo\Sdk\Providers\ApolloServiceProvider($app);
        $provider->boot();

        self::assertSame(['/host/resources/views'], $config->get('view.paths'));

        Container::setInstance(null);
    }

    public function testProviderPublishesApolloErrorPages(): void
    {
        $app = self::makeApplicationContainer();
        $app->instance('config', new Repository([
            'apollo' => array_replace_recursive(require __DIR__ . '/../../config/apollo.php', [
                'ignis_groups' => ['enabled' => false],
            ]),
            'view' => [
                'paths' => [],
            ],
        ]));
        Container::setInstance($app);

        $provider = new \Ometra\Apollo\Sdk\Providers\ApolloServiceProvider($app);
        $provider->boot();

        $paths = ServiceProvider::pathsToPublish(
            \Ometra\Apollo\Sdk\Providers\ApolloServiceProvider::class,
            'apollo-error-pages',
        );
        $viewsPath = (string) realpath(dirname(__DIR__, 2) . '/resources/views');

        self::assertSame([
            $viewsPath . '/errors' => resource_path('views/errors'),
        ], $paths);

        Container::setInstance(null);
    }

    public function testApolloErrorPageViewsExist(): void
    {
        $viewsPath = dirname(__DIR__, 2) . '/resources/views/errors';

        foreach (['layout', '401', '403', '404', '419', '429', '500', '503'] as $view) {
            self::assertFileExists($viewsPath . '/' . $view . '.blade.php');
        }
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

    private static function makeApplicationContainer(): Container
    {
        return new class extends Container {
            public function configPath(string $path = ''): string
            {
                return __DIR__ . '/../fixtures/config' . ($path !== '' ? DIRECTORY_SEPARATOR . $path : '');
            }

            public function resourcePath(string $path = ''): string
            {
                return __DIR__ . '/../fixtures/resources' . ($path !== '' ? DIRECTORY_SEPARATOR . $path : '');
            }
        };
    }
}
