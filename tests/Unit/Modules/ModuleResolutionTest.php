<?php

declare(strict_types=1);

use Illuminate\Config\Repository;
use Illuminate\Container\Container;
use Illuminate\Support\Facades\Facade;
use Ometra\Apollo\Sdk\Apollo;
use Ometra\Apollo\Sdk\Core\Config\ModuleConfigResolver;
use Ometra\Apollo\Sdk\Modules\Flare\FlareModule;
use Ometra\Apollo\Sdk\Modules\Ignis\IgnisModule;
use Ometra\Apollo\Sdk\Modules\Proteus\ProteusModule;
use Ometra\Apollo\Sdk\Modules\Pulse\PulseModule;
use PHPUnit\Framework\TestCase;

final class ModuleResolutionTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $app = new Container;
        Container::setInstance($app);
        Facade::clearResolvedInstances();
        Facade::setFacadeApplication($app);

        $app->instance('config', new Repository([
            'apollo' => [
                'modules' => [
                    'proteus' => ['base_url' => 'https://proteus.test'],
                    'pulse' => ['base_url' => 'https://pulse.test'],
                    'flare' => ['base_url' => 'https://flare.test'],
                    'ignis' => ['base_url' => 'https://ignis.test'],
                ],
            ],
        ]));
    }

    protected function tearDown(): void
    {
        Facade::clearResolvedInstances();
        Facade::setFacadeApplication(null);
        Container::setInstance(null);

        parent::tearDown();
    }

    public function test_apollo_entrypoint_returns_all_batch_one_modules(): void
    {
        $resolver = new ModuleConfigResolver;
        $apollo = new Apollo(
            new ProteusModule($resolver),
            new PulseModule($resolver),
            new FlareModule($resolver),
            new IgnisModule($resolver),
        );

        self::assertInstanceOf(ProteusModule::class, $apollo->proteus());
        self::assertInstanceOf(PulseModule::class, $apollo->pulse());
        self::assertInstanceOf(FlareModule::class, $apollo->flare());
        self::assertInstanceOf(IgnisModule::class, $apollo->ignis());
    }

    public function test_apollo_entrypoint_does_not_expose_flat_resource_methods(): void
    {
        $methods = get_class_methods(Apollo::class);

        self::assertNotContains('media', $methods);
        self::assertNotContains('mediaIndex', $methods);
        self::assertNotContains('__call', $methods);

        foreach ([ProteusModule::class, PulseModule::class, FlareModule::class, IgnisModule::class] as $module) {
            self::assertFalse(method_exists($module, 'config'));
        }
    }
}
