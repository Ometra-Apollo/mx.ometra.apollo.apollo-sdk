<?php

declare(strict_types=1);

namespace Ometra\Apollo\Sdk\Modules\Proteus\Resources;

use Ometra\Apollo\Sdk\Core\Http\ApolloHttpClient;

final class DirectoriesCollectionResource
{
    public function __construct(private readonly ApolloHttpClient $client) {}

    /** @param array<string, mixed> $filters */
    public function index(array $filters = []): array
    {
        return $this->client->userRequest('GET', 'directories', query: $filters);
    }

    /** @param array<string, mixed> $data */
    public function store(array $data): array
    {
        return $this->client->userRequest('POST', 'directories', payload: $data);
    }

    public function applicationGrants(string $applicationGrantId): DirectoryApplicationGrantResource
    {
        return new DirectoryApplicationGrantResource($this->client, $applicationGrantId);
    }
}
