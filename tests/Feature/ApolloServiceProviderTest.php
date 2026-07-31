<?php

declare(strict_types=1);

use Illuminate\Config\Repository;
use Illuminate\Container\Container;
use Illuminate\Support\Facades\Facade;
use Illuminate\Support\ServiceProvider;
use Ometra\Apollo\Sdk\Apollo;
use Ometra\Apollo\Sdk\Contracts\IgnisGroupContract;
use Ometra\Apollo\Sdk\Providers\ApolloServiceProvider;
use PHPUnit\Framework\TestCase;

final class ApolloServiceProviderTest extends TestCase
{
    public function test_apollo_classes_exist_and_provider_is_discoverable(): void
    {
        self::assertTrue(class_exists(Apollo::class));
        self::assertTrue(class_exists(ApolloServiceProvider::class));
        self::assertTrue(class_exists(Ometra\Apollo\Sdk\Facades\Apollo::class));
    }

    public function test_apollo_config_uses_exact_module_base_url_keys(): void
    {
        $config = require __DIR__.'/../../config/apollo.php';

        self::assertArrayHasKey('modules', $config);
        self::assertArrayHasKey('base_url', $config['modules']['proteus']);
        self::assertArrayHasKey('base_url', $config['modules']['pulse']);
        self::assertArrayHasKey('base_url', $config['modules']['flare']);
        self::assertArrayHasKey('base_url', $config['modules']['ignis']);
    }

    public function test_apollo_config_does_not_define_caronte_auth_configuration(): void
    {
        $config = require __DIR__.'/../../config/apollo.php';

        self::assertArrayNotHasKey('auth', $config);
        self::assertStringNotContainsString('CARONTE_', file_get_contents(__DIR__.'/../../config/apollo.php'));
    }

    public function test_apollo_config_defines_error_pages_opt_out(): void
    {
        $config = require __DIR__.'/../../config/apollo.php';

        self::assertArrayHasKey('error_pages', $config);
        self::assertArrayHasKey('enabled', $config['error_pages']);
    }

    public function test_provider_appends_apollo_views_as_error_page_fallback(): void
    {
        $app = self::makeApplicationContainer();
        $config = new Repository([
            'apollo' => array_replace_recursive(require __DIR__.'/../../config/apollo.php', [
                'error_pages' => ['enabled' => true],
                'ignis_groups' => ['enabled' => false],
            ]),
            'view' => [
                'paths' => ['/host/resources/views'],
            ],
        ]);

        $app->instance('config', $config);
        Container::setInstance($app);

        $provider = new ApolloServiceProvider($app);
        $provider->boot();

        $paths = $config->get('view.paths');

        self::assertSame('/host/resources/views', $paths[0]);
        self::assertSame((string) realpath(dirname(__DIR__, 2).'/resources/views'), $paths[1]);

        Container::setInstance(null);
    }

    public function test_provider_does_not_append_error_page_fallback_when_disabled(): void
    {
        $app = self::makeApplicationContainer();
        $config = new Repository([
            'apollo' => array_replace_recursive(require __DIR__.'/../../config/apollo.php', [
                'error_pages' => ['enabled' => false],
                'ignis_groups' => ['enabled' => false],
            ]),
            'view' => [
                'paths' => ['/host/resources/views'],
            ],
        ]);

        $app->instance('config', $config);
        Container::setInstance($app);

        $provider = new ApolloServiceProvider($app);
        $provider->boot();

        self::assertSame(['/host/resources/views'], $config->get('view.paths'));

        Container::setInstance(null);
    }

    public function test_provider_publishes_apollo_error_pages(): void
    {
        $app = self::makeApplicationContainer();
        $app->instance('config', new Repository([
            'apollo' => array_replace_recursive(require __DIR__.'/../../config/apollo.php', [
                'ignis_groups' => ['enabled' => false],
            ]),
            'view' => [
                'paths' => [],
            ],
        ]));
        Container::setInstance($app);

        $provider = new ApolloServiceProvider($app);
        $provider->boot();

        $paths = ServiceProvider::pathsToPublish(
            ApolloServiceProvider::class,
            'apollo-error-pages',
        );
        $viewsPath = (string) realpath(dirname(__DIR__, 2).'/resources/views');

        self::assertSame([
            $viewsPath.'/errors' => resource_path('views/errors'),
        ], $paths);

        Container::setInstance(null);
    }

    public function test_provider_publishes_shared_app_menu(): void
    {
        $app = self::makeApplicationContainer();
        $app->instance('config', new Repository([
            'apollo' => array_replace_recursive(require __DIR__.'/../../config/apollo.php', [
                'ignis_groups' => ['enabled' => false],
            ]),
            'view' => [
                'paths' => [],
            ],
        ]));
        Container::setInstance($app);

        $provider = new ApolloServiceProvider($app);
        $provider->boot();

        $paths = ServiceProvider::pathsToPublish(
            ApolloServiceProvider::class,
            'apollo-app-menu',
        );
        $appMenuPath = (string) realpath(dirname(__DIR__, 2).'/resources/js/shared/AppMenu');

        self::assertSame([
            $appMenuPath => resource_path('js/shared/AppMenu'),
        ], $paths);

        Container::setInstance(null);
    }

    public function test_provider_publishes_shared_directory_tree(): void
    {
        $app = self::makeApplicationContainer();
        $app->instance('config', new Repository([
            'apollo' => array_replace_recursive(require __DIR__.'/../../config/apollo.php', [
                'ignis_groups' => ['enabled' => false],
            ]),
            'view' => [
                'paths' => [],
            ],
        ]));
        Container::setInstance($app);

        $provider = new ApolloServiceProvider($app);
        $provider->boot();

        $paths = ServiceProvider::pathsToPublish(
            ApolloServiceProvider::class,
            'apollo-directory-tree',
        );
        $directoryTreePath = (string) realpath(dirname(__DIR__, 2).'/resources/js/shared/DirectoryTree');

        self::assertSame([
            $directoryTreePath => resource_path('js/shared/DirectoryTree'),
        ], $paths);

        Container::setInstance(null);
    }

    public function test_apollo_error_page_views_exist(): void
    {
        $viewsPath = dirname(__DIR__, 2).'/resources/views/errors';

        foreach (['layout', '401', '403', '404', '419', '429', '500', '503'] as $view) {
            self::assertFileExists($viewsPath.'/'.$view.'.blade.php');
        }
    }

    public function test_facade_resolves_apollo_singleton_from_container(): void
    {
        $app = new Container;
        Container::setInstance($app);
        Facade::clearResolvedInstances();
        Facade::setFacadeApplication($app);

        $app->instance('config', new Repository(['apollo' => require __DIR__.'/../../config/apollo.php']));

        $provider = new ApolloServiceProvider($app);
        $provider->register();

        self::assertInstanceOf(Apollo::class, Ometra\Apollo\Sdk\Facades\Apollo::getFacadeRoot());

        Facade::clearResolvedInstances();
        Facade::setFacadeApplication(null);
        Container::setInstance(null);
    }

    public function test_provider_requires_ignis_groups_binding_when_route_is_enabled(): void
    {
        $app = new Container;
        Container::setInstance($app);
        $app->instance('config', new Repository([
            'apollo' => array_replace_recursive(require __DIR__.'/../../config/apollo.php', [
                'ignis_groups' => ['enabled' => true],
            ]),
            'view' => ['paths' => []],
        ]));

        $provider = new ApolloServiceProvider($app);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage(
            'APOLLO_IGNIS_GROUPS_ENABLED=true requires a host binding for '
            .IgnisGroupContract::class
        );

        try {
            $provider->boot();
        } finally {
            Container::setInstance(null);
        }
    }

    public function test_provider_keeps_host_ignis_groups_binding(): void
    {
        $app = new Container;
        Container::setInstance($app);
        $app->instance('config', new Repository(['apollo' => require __DIR__.'/../../config/apollo.php']));
        $hostGroups = new class implements IgnisGroupContract
        {
            public function getGroups(): array
            {
                return [];
            }
        };
        $app->instance(IgnisGroupContract::class, $hostGroups);

        $provider = new ApolloServiceProvider($app);
        $provider->register();

        self::assertSame($hostGroups, $app->make(IgnisGroupContract::class));

        Container::setInstance(null);
    }

    private static function makeApplicationContainer(): Container
    {
        return new class extends Container
        {
            public function configPath(string $path = ''): string
            {
                return __DIR__.'/../fixtures/config'.($path !== '' ? DIRECTORY_SEPARATOR.$path : '');
            }

            public function resourcePath(string $path = ''): string
            {
                return __DIR__.'/../fixtures/resources'.($path !== '' ? DIRECTORY_SEPARATOR.$path : '');
            }
        };
    }
}
