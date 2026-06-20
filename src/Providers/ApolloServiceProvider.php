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

        if ((bool) config('apollo.ignis_groups.enabled', false)) {
            $this->loadRoutesFrom(__DIR__ . '/../routes/api.php');
        }
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
