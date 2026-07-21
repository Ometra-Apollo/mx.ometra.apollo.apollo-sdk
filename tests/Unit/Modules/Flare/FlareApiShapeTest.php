<?php

declare(strict_types=1);

use Illuminate\Config\Repository;
use Illuminate\Container\Container;
use Illuminate\Support\Facades\Facade;
use Ometra\Apollo\Sdk\Core\Config\ModuleConfigResolver;
use Ometra\Apollo\Sdk\Modules\Flare\FlareModule;
use Ometra\Apollo\Sdk\Modules\Flare\Resources\PlaylistsResource;
use Ometra\Apollo\Sdk\Modules\Flare\Resources\StationsResource;
use PHPUnit\Framework\TestCase;

final class FlareApiShapeTest extends TestCase
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
                    'flare' => ['base_url' => 'https://flare.test'],
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

    public function test_flare_module_exposes_stations_resource(): void
    {
        $module = new FlareModule(new ModuleConfigResolver);

        self::assertInstanceOf(StationsResource::class, $module->stations());
        self::assertInstanceOf(PlaylistsResource::class, $module->playlists('playlist-1'));
        self::assertFalse(method_exists(FlareModule::class, 'config'));
    }
}
