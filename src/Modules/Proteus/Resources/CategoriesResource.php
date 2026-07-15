<?php

declare(strict_types=1);

namespace Ometra\Apollo\Sdk\Modules\Proteus\Resources;

use Ometra\Apollo\Sdk\Core\Http\ApolloHttpClient;

final class CategoriesResource
{
    public function __construct(private readonly ApolloHttpClient $client) {}

    /** @param array<string, mixed> $data */
    public function index(array $data = []): array
    {
        return $this->client->applicationRequest('GET', 'categories', query: $data);
    }

    /** @param array<string, mixed> $data */
    public function store(array $data): array
    {
        return $this->client->applicationRequest('POST', 'configuration/categories', payload: $data);
    }

    public function show(string $id_category): array
    {
        return $this->client->applicationRequest('GET', 'configuration/categories/' . $id_category);
    }

    /** @param array<string, mixed> $data */
    public function update(string $id_category, array $data): array
    {
        return $this->client->applicationRequest('PUT', 'configuration/categories/' . $id_category, payload: $data);
    }

    public function delete(string $id_category): ?array
    {
        return $this->client->applicationRequest('DELETE', 'configuration/categories/' . $id_category);
    }

    public function setDefault(string $id_category): array
    {
        return $this->client->applicationRequest('PATCH', 'configuration/categories/' . $id_category . '/default');
    }
}
