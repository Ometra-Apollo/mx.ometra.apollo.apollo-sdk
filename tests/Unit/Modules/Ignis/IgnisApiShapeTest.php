<?php

declare(strict_types=1);

use Illuminate\Config\Repository;
use Illuminate\Container\Container;
use Illuminate\Support\Facades\Facade;
use Ometra\Apollo\Sdk\Core\Config\ModuleConfigResolver;
use Ometra\Apollo\Sdk\Modules\Ignis\IgnisModule;
use Ometra\Apollo\Sdk\Modules\Ignis\Resources\CampaignCollectionResource;
use Ometra\Apollo\Sdk\Modules\Ignis\Resources\CampaignContentLightPathResource;
use Ometra\Apollo\Sdk\Modules\Ignis\Resources\CampaignContentsResource;
use Ometra\Apollo\Sdk\Modules\Ignis\Resources\CampaignResource;
use Ometra\Apollo\Sdk\Modules\Ignis\Resources\GroupResource;
use PHPUnit\Framework\TestCase;

final class IgnisApiShapeTest extends TestCase
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

    public function test_ignis_module_exposes_bound_group_resources(): void
    {
        $module = new IgnisModule(new ModuleConfigResolver);

        $group = $module->groups('group-1');
        self::assertInstanceOf(GroupResource::class, $group);
        self::assertInstanceOf(CampaignCollectionResource::class, $group->campaigns());
        self::assertInstanceOf(CampaignResource::class, $group->campaigns(11));
        self::assertInstanceOf(CampaignContentsResource::class, $group->campaigns(11)->contents());
        self::assertInstanceOf(CampaignContentLightPathResource::class, $group->campaigns(11)->contents()->lightpath());
        self::assertFalse(method_exists(IgnisModule::class, 'externalGroups'));
        self::assertFalse(method_exists(IgnisModule::class, 'campaigns'));
        self::assertFalse(method_exists(IgnisModule::class, 'contentHits'));
        self::assertFalse(method_exists(IgnisModule::class, 'config'));
    }
}
