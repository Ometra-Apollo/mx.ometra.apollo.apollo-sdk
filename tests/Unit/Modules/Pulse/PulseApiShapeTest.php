<?php

declare(strict_types=1);

use Illuminate\Config\Repository;
use Illuminate\Container\Container;
use Illuminate\Support\Facades\Facade;
use Ometra\Apollo\Sdk\Core\Config\ModuleConfigResolver;
use Ometra\Apollo\Sdk\Modules\Pulse\PulseModule;
use Ometra\Apollo\Sdk\Modules\Pulse\Resources\GroupsResource;
use PHPUnit\Framework\TestCase;

final class PulseApiShapeTest extends TestCase
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
                    'pulse' => ['base_url_env' => 'PULSE_BASE_URL', 'base_url' => 'https://pulse.test'],
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

    public function testPulseModuleExposesGroupsResource(): void
    {
        $module = new PulseModule(new ModuleConfigResolver());

        self::assertInstanceOf(GroupsResource::class, $module->groups());
    }
}
