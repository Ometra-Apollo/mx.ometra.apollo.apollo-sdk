<?php

declare(strict_types=1);

use Illuminate\Config\Repository;
use Illuminate\Container\Container;
use Illuminate\Support\Facades\Facade;
use Ometra\Apollo\Sdk\Core\Config\ModuleConfigResolver;
use Ometra\Apollo\Sdk\Modules\Ignis\IgnisModule;
use Ometra\Apollo\Sdk\Modules\Ignis\Resources\CampaignsResource;
use Ometra\Apollo\Sdk\Modules\Ignis\Resources\ContentHitsResource;
use PHPUnit\Framework\TestCase;

final class IgnisApiShapeTest extends TestCase
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
                    'ignis' => ['base_url_env' => 'IGNIS_BASE_URL', 'base_url' => 'https://ignis.test'],
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

    public function testIgnisModuleExposesResources(): void
    {
        $module = new IgnisModule(new ModuleConfigResolver());

        self::assertInstanceOf(CampaignsResource::class, $module->campaigns());
        self::assertInstanceOf(ContentHitsResource::class, $module->contentHits());
    }
}
