<?php

declare(strict_types=1);

use Illuminate\Config\Repository;
use Illuminate\Container\Container;
use Illuminate\Support\Facades\Facade;
use Ometra\Apollo\Sdk\Core\Config\ModuleConfigResolver;
use Ometra\Apollo\Sdk\Modules\Flare\FlareModule;
use Ometra\Apollo\Sdk\Modules\Flare\Resources\StationsResource;
use PHPUnit\Framework\TestCase;

final class FlareApiShapeTest extends TestCase
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
                    'flare' => ['base_url_env' => 'FLARE_BASE_URL', 'base_url' => 'https://flare.test'],
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

    public function testFlareModuleExposesStationsResource(): void
    {
        $module = new FlareModule(new ModuleConfigResolver());

        self::assertInstanceOf(StationsResource::class, $module->stations());
    }
}
