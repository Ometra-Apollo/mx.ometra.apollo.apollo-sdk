<?php

declare(strict_types=1);

use Ometra\Apollo\Sdk\Modules\Flare\FlareModule;
use Ometra\Apollo\Sdk\Modules\Flare\Resources\PlaylistsResource;
use Ometra\Apollo\Sdk\Modules\Flare\Resources\StationsResource;
use Ometra\Apollo\Sdk\Modules\Ignis\IgnisModule;
use Ometra\Apollo\Sdk\Modules\Proteus\ProteusModule;
use Ometra\Apollo\Sdk\Modules\Proteus\Resources\CategoriesResource;
use Ometra\Apollo\Sdk\Modules\Proteus\Resources\DirectoriesCollectionResource;
use Ometra\Apollo\Sdk\Modules\Proteus\Resources\LightPathResource;
use Ometra\Apollo\Sdk\Modules\Proteus\Resources\MediaCollectionResource;
use Ometra\Apollo\Sdk\Modules\Proteus\Resources\MediaResource;
use Ometra\Apollo\Sdk\Modules\Pulse\PulseModule;
use Ometra\Apollo\Sdk\Modules\Pulse\Resources\GroupsResource;
use PHPUnit\Framework\TestCase;

final class RemovedApiGuardTest extends TestCase
{
    public function test_removed_module_entrypoints_do_not_return(): void
    {
        $removed = [
            ProteusModule::class => ['config', 'metadata', 'presets'],
            FlareModule::class => ['config'],
            PulseModule::class => ['config'],
            IgnisModule::class => ['config', 'campaigns', 'contentHits', 'externalGroups'],
        ];

        foreach ($removed as $class => $methods) {
            foreach ($methods as $method) {
                self::assertFalse(method_exists($class, $method), $class.'::'.$method.' was restored.');
            }
        }
    }

    public function test_removed_resource_actions_do_not_return(): void
    {
        $removed = [
            MediaCollectionResource::class => ['upload', 'create', 'tags', 'show', 'delete', 'setMetadata', 'lightPathUrl', 'downloadUrl'],
            MediaResource::class => ['upload', 'create', 'delete', 'setMetadata', 'lightPathUrl', 'showWithUserToken'],
            DirectoriesCollectionResource::class => ['create', 'show', 'update', 'delete', 'grantApplication', 'grantApplicationWithUserToken', 'updateApplicationGrant', 'revokeApplicationGrant'],
            LightPathResource::class => ['extendGrant', 'deleteGrant'],
            CategoriesResource::class => ['show', 'update', 'delete', 'setDefault'],
            StationsResource::class => ['index', 'show', 'showByGroup', 'assignGroups', 'detachGroup', 'invalidateGroupCatalogCache'],
            PlaylistsResource::class => ['index', 'store', 'delete'],
            GroupsResource::class => ['invalidateStationCache'],
        ];

        foreach ($removed as $class => $methods) {
            foreach ($methods as $method) {
                self::assertFalse(method_exists($class, $method), $class.'::'.$method.' was restored.');
            }
        }
    }

    public function test_removed_resource_classes_and_campaign_dtos_do_not_return(): void
    {
        foreach ([
            'Ometra\\Apollo\\Sdk\\Modules\\Proteus\\Resources\\DirectoriesResource',
            'Ometra\\Apollo\\Sdk\\Modules\\Proteus\\Resources\\MetadataResource',
            'Ometra\\Apollo\\Sdk\\Modules\\Proteus\\Resources\\PresetsResource',
            'Ometra\\Apollo\\Sdk\\Modules\\Ignis\\Resources\\CampaignsResource',
            'Ometra\\Apollo\\Sdk\\Modules\\Ignis\\Resources\\ContentHitsResource',
            'Ometra\\Apollo\\Sdk\\Modules\\Ignis\\Resources\\ExternalGroupResource',
            'Ometra\\Apollo\\Sdk\\DTO\\IgnisCampaignDTO',
            'Ometra\\Apollo\\Sdk\\DTO\\IgnisCampaignDetailDTO',
            'Ometra\\Apollo\\Sdk\\DTO\\IgnisCampaignContentDTO',
            'Ometra\\Apollo\\Sdk\\DTO\\IgnisCampaignContentScheduleDTO',
            'Ometra\\Apollo\\Sdk\\DTO\\IgnisCampaignPlayModifiersDTO',
        ] as $class) {
            self::assertFalse(class_exists($class), $class.' was restored.');
        }
    }
}
