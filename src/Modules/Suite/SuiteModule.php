<?php

declare(strict_types=1);

namespace Ometra\Apollo\Sdk\Modules\Suite;

use Ometra\Apollo\Sdk\Core\Config\ModuleConfigResolver;
use Ometra\Apollo\Sdk\Core\Http\ApolloHttpClient;
use Ometra\Apollo\Sdk\Modules\Suite\Resources\ApplicationsResource;

final class SuiteModule
{
    private ?ApolloHttpClient $client = null;

    public function __construct(private readonly ModuleConfigResolver $configResolver) {}

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

    private function client(): ApolloHttpClient
    {
        if ($this->client === null) {
            $this->client = new ApolloHttpClient($this->config()['base_url']);
        }

        return $this->client;
    }
}
