<?php

declare(strict_types=1);

namespace Ometra\Apollo\Sdk\Providers;

use Illuminate\Support\ServiceProvider;
use Ometra\Apollo\Sdk\Apollo;
use Ometra\Apollo\Sdk\Contracts\IgnisGroupContract;
use Ometra\Apollo\Sdk\Core\Config\ModuleConfigResolver;
use Ometra\Apollo\Sdk\Modules\Flare\FlareModule;
use Ometra\Apollo\Sdk\Modules\Ignis\IgnisModule;
use Ometra\Apollo\Sdk\Modules\Proteus\ProteusModule;
use Ometra\Apollo\Sdk\Modules\Pulse\PulseModule;
use RuntimeException;

final class ApolloServiceProvider extends ServiceProvider
{
    private const CONFIG_PATH = __DIR__ . '/../../config/apollo.php';
    private const APP_MENU_PATH = __DIR__ . '/../../resources/js/shared/AppMenu';
    private const DIRECTORY_TREE_PATH = __DIR__ . '/../../resources/js/shared/DirectoryTree';
    private const VIEWS_PATH = __DIR__ . '/../../resources/views';

    public function register(): void
    {
        $this->mergeConfigFrom(self::CONFIG_PATH, 'apollo');

        $this->app->singleton(ModuleConfigResolver::class);
        $this->app->singleton(ProteusModule::class);
        $this->app->singleton(PulseModule::class);
        $this->app->singleton(FlareModule::class);
        $this->app->singleton(IgnisModule::class);

        $this->app->singleton(Apollo::class, function ($app): Apollo {
            return new Apollo(
                $app->make(ProteusModule::class),
                $app->make(PulseModule::class),
                $app->make(FlareModule::class),
                $app->make(IgnisModule::class),
            );
        });

        $this->app->alias(Apollo::class, 'apollo');
    }

    public function boot(): void
    {
        $this->publishes([
            self::CONFIG_PATH => config_path('apollo.php'),
        ], 'apollo-config');

        $this->publishes([
            self::viewsPath() . '/errors' => resource_path('views/errors'),
        ], 'apollo-error-pages');

        $this->publishes([
            self::appMenuPath() => resource_path('js/shared/AppMenu'),
        ], 'apollo-app-menu');

        $this->publishes([
            self::directoryTreePath() => resource_path('js/shared/DirectoryTree'),
        ], 'apollo-directory-tree');

        $this->publishes([
            self::CONFIG_PATH => config_path('apollo.php'),
            self::appMenuPath() => resource_path('js/shared/AppMenu'),
            self::directoryTreePath() => resource_path('js/shared/DirectoryTree'),
            self::viewsPath() . '/errors' => resource_path('views/errors'),
        ], 'apollo');

        $this->registerErrorPageViewFallback();

        if (method_exists($this->app, 'routesAreCached')) {
            $this->loadRoutesFrom(__DIR__ . '/../routes/api.php');
            $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');
        }

        if ((bool) config('apollo.ignis_groups.enabled', false)) {
            $this->ensureIgnisGroupsBinding();
        }
    }

    /**
     * Let host applications resolve SDK error pages without publishing files.
     *
     * Laravel looks for HTTP error pages under the configured view paths as
     * `errors/{status}.blade.php`. App paths stay first so local overrides keep
     * priority, while Apollo views act as a package fallback.
     */
    private function registerErrorPageViewFallback(): void
    {
        if (!(bool) config('apollo.error_pages.enabled', true)) {
            return;
        }

        /** @var \Illuminate\Contracts\Config\Repository $config */
        $config = $this->app['config'];
        $paths = (array) $config->get('view.paths', []);
        $viewsPath = self::viewsPath();

        if (in_array($viewsPath, $paths, true)) {
            return;
        }

        $paths[] = $viewsPath;

        $config->set('view.paths', $paths);
    }

    private static function viewsPath(): string
    {
        return (string) realpath(self::VIEWS_PATH) ?: self::VIEWS_PATH;
    }

    private static function appMenuPath(): string
    {
        return (string) realpath(self::APP_MENU_PATH) ?: self::APP_MENU_PATH;
    }

    private static function directoryTreePath(): string
    {
        return (string) realpath(self::DIRECTORY_TREE_PATH) ?: self::DIRECTORY_TREE_PATH;
    }

    private function ensureIgnisGroupsBinding(): void
    {
        if ($this->app->bound(IgnisGroupContract::class)) {
            return;
        }

        throw new RuntimeException(
            'APOLLO_IGNIS_GROUPS_ENABLED=true requires a host binding for ' . IgnisGroupContract::class . '.'
        );
    }
}
