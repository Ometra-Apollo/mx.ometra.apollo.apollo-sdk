<?php

declare(strict_types=1);

namespace Ometra\Apollo\Sdk\Modules\Suite;

use Ometra\Apollo\Sdk\Core\Config\ModuleConfigResolver;
use Ometra\Apollo\Sdk\Core\Http\ApolloHttpClient;
use Ometra\Apollo\Sdk\Modules\Suite\Resources\ApplicationsResource;
use Ometra\Apollo\Sdk\Modules\Suite\Resources\ClientsResource;
use Ometra\Apollo\Sdk\Modules\Suite\Resources\GroupsResource;
use Ometra\Apollo\Sdk\Modules\Suite\Resources\NotificationsResources;
use Ometra\Apollo\Sdk\Modules\Suite\Resources\UsersResources;

final class SuiteModule
{
    private ?ApolloHttpClient $client = null;

    private bool $asApplication = false;

    public function __construct(private readonly ModuleConfigResolver $configResolver) {}

    public function asApplication(): static
    {
        $clone = clone $this;
        $clone->asApplication = true;
        $clone->client = null;

        return $clone;
    }

    /**
     * @return array{base_url: string}
     */
    public function config(): array
    {
        return $this->configResolver->resolve('suite');
    }

    public function applications(): ApplicationsResource
    {
        return new ApplicationsResource($this->client());
    }

    public function users(): UsersResources
    {
        return new UsersResources($this->client());
    }

    public function clients(): ClientsResource
    {
        return new ClientsResource($this->client());
    }

    public function groups(): GroupsResource
    {
        return new GroupsResource($this->client());
    }

    public function notifications(): NotificationsResources
    {
        return new NotificationsResources($this->client());
    }

    private function client(): ApolloHttpClient
    {
        if ($this->client === null) {
            $this->client = new ApolloHttpClient(
                $this->configResolver->resolve('suite')['base_url'],
                $this->asApplication,
            );
        }

        return $this->client;
    }
}
