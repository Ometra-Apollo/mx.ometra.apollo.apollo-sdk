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
use Ometra\Apollo\Sdk\Modules\Proteus\Resources\CategoriesResource;
use Ometra\Apollo\Sdk\Modules\Proteus\Resources\DirectoriesResource;
use Ometra\Apollo\Sdk\Modules\Proteus\Resources\MediaResource;
use Ometra\Apollo\Sdk\Modules\Proteus\Resources\MetadataResource;
use Ometra\Apollo\Sdk\Modules\Proteus\Resources\PresetsResource;
use Ometra\Apollo\Sdk\Modules\Pulse\PulseModule;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class ProteusApiShapeTest extends TestCase
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

    public function testProteusModuleExposesRealResourceObjects(): void
    {
        $module = new ProteusModule(new ModuleConfigResolver());

        self::assertInstanceOf(MediaResource::class, $module->media());
        self::assertInstanceOf(MetadataResource::class, $module->metadata());
        self::assertInstanceOf(CategoriesResource::class, $module->categories());
        self::assertInstanceOf(DirectoriesResource::class, $module->directories());
        self::assertInstanceOf(PresetsResource::class, $module->presets());
    }

    public function testApolloEntrypointStillRejectsFlatRootProteusMethods(): void
    {
        $resolver = new ModuleConfigResolver();
        $apollo = new Apollo(
            new ProteusModule($resolver),
            new PulseModule($resolver),
            new FlareModule($resolver),
            new IgnisModule($resolver),
        );

        self::assertFalse(method_exists($apollo, 'media'));
        self::assertFalse(method_exists($apollo, 'mediaIndex'));
        self::assertFalse(method_exists($apollo, 'metadataKeys'));
        self::assertFalse(method_exists($apollo, 'categoriesIndex'));
        self::assertSame(['proteus', 'pulse', 'flare', 'ignis'], array_values(array_filter(
            get_class_methods(Apollo::class),
            static fn (string $method): bool => ! str_starts_with($method, '__'),
        )));
    }

    /**
     * @param  class-string  $resourceClass
     * @param  list<string>  $forbiddenMethods
     */
    #[DataProvider('forbiddenResourceMethods')]
    public function testProteusResourcesDoNotExposeRedundantLegacyMethodNames(
        string $resourceClass,
        array $forbiddenMethods,
    ): void {
        $methods = get_class_methods($resourceClass);

        foreach ($forbiddenMethods as $method) {
            self::assertNotContains($method, $methods, $resourceClass . ' must not expose ' . $method);
        }
    }

    /**
     * @return array<string, array{0: class-string, 1: list<string>}>
     */
    public static function forbiddenResourceMethods(): array
    {
        return [
            'media' => [MediaResource::class, ['mediaIndex', 'mediaUpload', 'mediaSetMetadata', 'mediaDelete', 'saveMediaLocal']],
            'metadata' => [MetadataResource::class, ['metadataKeys', 'metadataValuesFromKey', 'metadataIndex', 'metadataShow']],
            'categories' => [CategoriesResource::class, ['categoriesIndex', 'categoryStore', 'categoryShow', 'categoryUpdate', 'categoryDelete']],
            'directories' => [DirectoriesResource::class, ['directoriesIndex', 'directoryCreate', 'directoryStore', 'directoryShow', 'directoryUpdate', 'directoryDelete']],
            'presets' => [PresetsResource::class, ['presetIndex', 'presetStore', 'presetShow', 'presetUpdate', 'presetDelete']],
        ];
    }
}
