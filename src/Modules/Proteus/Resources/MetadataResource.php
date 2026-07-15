<?php

declare(strict_types=1);

namespace Ometra\Apollo\Sdk\Modules\Proteus\Resources;

use Ometra\Apollo\Sdk\Core\Http\ApolloHttpClient;

final class MetadataResource
{
    public function __construct(private readonly ApolloHttpClient $client) {}

    public function keys(string $key): array
    {
        return $this->client->applicationRequest('GET', 'media/metadata/' . $key);
    }

    public function values(string $key): array
    {
        return $this->client->applicationRequest('GET', 'media/metadata/' . $key . '/values');
    }

    /** @param array<string, mixed> $query */
    public function index(string $id_media, array $query = []): array
    {
        return $this->client->userRequest('GET', 'media/' . $id_media . '/metadata', query: $query);
    }

    public function show(string $id_media, string $key): array
    {
        return $this->client->userRequest('GET', 'media/' . $id_media . '/metadata/' . $key);
    }

    /** @param array<string, mixed> $data */
    public function store(string $id_media, array $data): array
    {
        return $this->client->userRequest('POST', 'media/' . $id_media . '/metadata', payload: $data);
    }

    /** @param array<string, mixed> $data */
    public function update(string $id_media, array $data): array
    {
        return $this->client->userRequest('PUT', 'media/' . $id_media . '/metadata', payload: $data);
    }

    public function delete(string $id_media, string $key): ?array
    {
        return $this->client->userRequest('DELETE', 'media/' . $id_media . '/metadata/' . $key);
    }
}
