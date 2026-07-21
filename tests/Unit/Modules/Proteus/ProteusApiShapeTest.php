<?php

declare(strict_types=1);

use Illuminate\Config\Repository;
use Illuminate\Container\Container;
use Ometra\Apollo\Sdk\Core\Config\ModuleConfigResolver;
use Ometra\Apollo\Sdk\Modules\Proteus\ProteusModule;
use Ometra\Apollo\Sdk\Modules\Proteus\Resources\DirectoriesCollectionResource;
use Ometra\Apollo\Sdk\Modules\Proteus\Resources\DirectoryApplicationGrantRequestResource;
use Ometra\Apollo\Sdk\Modules\Proteus\Resources\DirectoryApplicationGrantResource;
use Ometra\Apollo\Sdk\Modules\Proteus\Resources\DirectoryResource;
use Ometra\Apollo\Sdk\Modules\Proteus\Resources\LightPathRequestResource;
use Ometra\Apollo\Sdk\Modules\Proteus\Resources\LightPathResource;
use Ometra\Apollo\Sdk\Modules\Proteus\Resources\MediaCollectionResource;
use Ometra\Apollo\Sdk\Modules\Proteus\Resources\MediaMetadataCollectionResource;
use Ometra\Apollo\Sdk\Modules\Proteus\Resources\MediaMetadataResource;
use Ometra\Apollo\Sdk\Modules\Proteus\Resources\MediaResource;
use PHPUnit\Framework\TestCase;

final class ProteusApiShapeTest extends TestCase
{
    protected function setUp(): void
    {
        $app = new Container;
        $app->instance('config', new Repository(['apollo.modules.proteus.base_url' => 'https://proteus.test']));
        Container::setInstance($app);
    }

    protected function tearDown(): void
    {
        Container::setInstance(null);
    }

    public function test_collection_and_bound_resources_have_distinct_types(): void
    {
        $module = new ProteusModule(new ModuleConfigResolver);

        self::assertInstanceOf(MediaCollectionResource::class, $module->media());
        self::assertInstanceOf(MediaResource::class, $module->media('media-1'));
        self::assertInstanceOf(DirectoriesCollectionResource::class, $module->directories());
        self::assertInstanceOf(DirectoryResource::class, $module->directories('dir-1'));
        self::assertInstanceOf(LightPathResource::class, $module->lightPath('grant-1'));
    }

    public function test_nested_resources_are_bound_to_their_parent(): void
    {
        $module = new ProteusModule(new ModuleConfigResolver);

        self::assertInstanceOf(MediaMetadataCollectionResource::class, $module->media('media-1')->metadata());
        self::assertInstanceOf(MediaMetadataResource::class, $module->media('media-1')->metadata('author'));
        self::assertInstanceOf(LightPathRequestResource::class, $module->media('media-1')->lightPath());
        self::assertInstanceOf(DirectoryApplicationGrantRequestResource::class, $module->directories('dir-1')->applicationGrants());
        self::assertInstanceOf(DirectoryApplicationGrantResource::class, $module->directories()->applicationGrants('grant-1'));
    }

    public function test_removed_api_names_are_absent(): void
    {
        foreach (['upload', 'create', 'delete', 'setMetadata', 'lightPathUrl', 'showWithUserToken'] as $method) {
            self::assertFalse(method_exists(MediaResource::class, $method));
            self::assertFalse(method_exists(MediaCollectionResource::class, $method));
        }

        foreach (['metadata', 'presets', 'config'] as $method) {
            self::assertFalse(method_exists(ProteusModule::class, $method));
        }
    }
}
