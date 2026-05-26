<?php

declare(strict_types=1);

namespace Ometra\Apollo\Sdk\Modules\Proteus\Resources;

use Ometra\Apollo\Sdk\Core\Http\ApolloHttpClient;

final class CategoriesResource
{
    public function __construct(private readonly ApolloHttpClient $client)
    {
    }

    /** @param array<string, mixed> $data */
    public function index(array $data = []): array
    {
        return $this->client->applicationRequest('GET', 'categories', query: $data);
    }

    /** @param array<string, mixed> $data */
    public function store(array $data): array
    {
        return $this->client->applicationRequest('POST', 'categories', payload: $data);
    }

    public function show(string $id): array
    {
        return $this->client->applicationRequest('GET', 'categories/' . $id);
    }

    /** @param array<string, mixed> $data */
    public function update(string $id, array $data): array
    {
        return $this->client->applicationRequest('PUT', 'categories/' . $id, payload: $data);
    }

    public function delete(string $id): ?array
    {
        return $this->client->applicationRequest('DELETE', 'categories/' . $id);
    }
}
