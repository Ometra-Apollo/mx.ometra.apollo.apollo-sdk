<?php

declare(strict_types=1);

namespace Ometra\Apollo\Sdk\Providers;

use Illuminate\Support\ServiceProvider;
use Ometra\Apollo\Sdk\Apollo;
use Ometra\Apollo\Sdk\Core\Config\ModuleConfigResolver;
use Ometra\Apollo\Sdk\Modules\Flare\FlareModule;
use Ometra\Apollo\Sdk\Modules\Ignis\IgnisModule;
use Ometra\Apollo\Sdk\Modules\Proteus\ProteusModule;
use Ometra\Apollo\Sdk\Modules\Pulse\PulseModule;

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
    }

    public function boot(): void
    {
        $this->publishes([
            self::CONFIG_PATH => config_path('apollo.php'),
        ], 'apollo-config');
    }
}
