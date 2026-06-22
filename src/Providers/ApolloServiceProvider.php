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

        $this->registerIgnisGroupsBinding();
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
            self::CONFIG_PATH => config_path('apollo.php'),
            self::viewsPath() . '/errors' => resource_path('views/errors'),
        ], 'apollo');

        $this->registerErrorPageViewFallback();

        if ((bool) config('apollo.ignis_groups.enabled', false)) {
            $this->loadRoutesFrom(__DIR__ . '/../routes/api.php');
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

    /**
     * Bind {@see IgnisGroupContract} to the configured host implementation.
     *
     * Uses `bind` (not `singleton`) so host implementations carrying
     * request-scoped dependencies are not shared across requests. Throws
     * eagerly when the implementation is empty or does not implement the
     * contract, so misconfiguration fails at boot rather than at first request.
     */
    private function registerIgnisGroupsBinding(): void
    {
        $implementation = (string) config('apollo.ignis_groups.implementation', '');

        if ($implementation === '' || !is_a($implementation, IgnisGroupContract::class, true)) {
            throw new RuntimeException(
                'apollo.ignis_groups.implementation must be a non-empty class string implementing '
                . IgnisGroupContract::class . '.'
            );
        }

        $this->app->bind(IgnisGroupContract::class, $implementation);
    }
}
