<?php

declare(strict_types=1);

use Illuminate\Config\Repository;
use Illuminate\Container\Container;
use Illuminate\Support\Facades\Facade;
use Ometra\Apollo\Sdk\Core\Config\ModuleConfigResolver;
use Ometra\Apollo\Sdk\Modules\Suite\Resources\ApplicationsResource;
use Ometra\Apollo\Sdk\Modules\Suite\SuiteModule;
use PHPUnit\Framework\TestCase;

final class SuiteApiShapeTest extends TestCase
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
                    'suite' => ['base_url' => 'https://apollo.test'],
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

    public function test_suite_module_exposes_applications_resource(): void
    {
        $module = new SuiteModule(new ModuleConfigResolver);

        self::assertInstanceOf(ApplicationsResource::class, $module->applications());
    }
}
