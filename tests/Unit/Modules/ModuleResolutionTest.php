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

        $app = new Container();
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

    public function testApolloEntrypointReturnsAllBatchOneModules(): void
    {
        $resolver = new ModuleConfigResolver();
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

    public function testEachModuleResolvesItsOwnExactBaseUrlConfig(): void
    {
        $resolver = new ModuleConfigResolver();

        self::assertSame([
            'base_url' => 'https://proteus.test',
        ], (new ProteusModule($resolver))->config());
        self::assertSame([
            'base_url' => 'https://pulse.test',
        ], (new PulseModule($resolver))->config());
        self::assertSame([
            'base_url' => 'https://flare.test',
        ], (new FlareModule($resolver))->config());
        self::assertSame([
            'base_url' => 'https://ignis.test',
        ], (new IgnisModule($resolver))->config());
    }

    public function testApolloEntrypointDoesNotExposeFlatResourceMethods(): void
    {
        $methods = get_class_methods(Apollo::class);

        self::assertNotContains('media', $methods);
        self::assertNotContains('mediaIndex', $methods);
        self::assertNotContains('__call', $methods);
    }
}
