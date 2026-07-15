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
    public function index(string $mediaId, array $query = []): array
    {
        return $this->client->userRequest('GET', 'media/' . $mediaId . '/metadata', query: $query);
    }

    public function show(string $mediaId, string $key): array
    {
        return $this->client->userRequest('GET', 'media/' . $mediaId . '/metadata/' . $key);
    }

    /** @param array<string, mixed> $data */
    public function store(string $mediaId, array $data): array
    {
        return $this->client->userRequest('POST', 'media/' . $mediaId . '/metadata', payload: $data);
    }

    /** @param array<string, mixed> $data */
    public function update(string $mediaId, array $data): array
    {
        return $this->client->userRequest('PUT', 'media/' . $mediaId . '/metadata', payload: $data);
    }

    public function delete(string $mediaId, string $key): ?array
    {
        return $this->client->userRequest('DELETE', 'media/' . $mediaId . '/metadata/' . $key);
    }
}
