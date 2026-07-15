<?php

declare(strict_types=1);

use Illuminate\Config\Repository;
use Illuminate\Container\Container;
use Illuminate\Support\Facades\Facade;
use Ometra\Apollo\Sdk\DTO\ExternalGroupDTO;
use PHPUnit\Framework\TestCase;

final class ExternalGroupDTOTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $app = new Container();
        Container::setInstance($app);
        Facade::clearResolvedInstances();
        Facade::setFacadeApplication($app);
        $app->instance('config', new Repository(['app' => ['name' => '']]));
    }

    protected function tearDown(): void
    {
        Facade::clearResolvedInstances();
        Facade::setFacadeApplication(null);
        Container::setInstance(null);

        parent::tearDown();
    }

    public function testToArrayOmitsOnlyNullOptionalValues(): void
    {
        $dto = new ExternalGroupDTO(
            name: '',
            external_id: '0',
            media_type: [],
        );

        self::assertSame([
            'name' => '',
            'external_id' => '0',
            'media_type' => [],
            'provider_id' => '',
        ], $dto->toArray());
    }
}
