<?php

declare(strict_types=1);

namespace Ometra\Apollo\Sdk\Modules\Proteus\Resources;

use Ometra\Apollo\Sdk\Core\Http\ApolloHttpClient;

final class DirectoriesResource
{
    public function __construct(private readonly ApolloHttpClient $client)
    {
    }

    /** @param array<string, mixed> $data */
    public function index(array $data = []): array
    {
        return $this->client->userRequest('GET', 'directories', query: $data);
    }

    public function create(?string $parentId = null): array
    {
        $endpoint = 'directories/create';

        if ($parentId !== null && $parentId !== '') {
            $endpoint .= '/' . $parentId;
        }

        return $this->client->userRequest('GET', $endpoint);
    }

    /** @param array<string, mixed> $data */
    public function store(array $data): array
    {
        return $this->client->userRequest('POST', 'directories', payload: $data);
    }

    public function show(string $id): array
    {
        return $this->client->userRequest('GET', 'directories/' . $id);
    }

    /** @param array<string, mixed> $data */
    public function update(string $id, array $data): array
    {
        return $this->client->userRequest('PUT', 'directories/' . $id, payload: $data);
    }

    public function delete(string $id): ?array
    {
        return $this->client->userRequest('DELETE', 'directories/' . $id);
    }

    public function setVisibility(string $id, string $visibility): array
    {
        return $this->client->userRequest('POST', 'directories/' . $id . '/set-visibility', payload: ['visibility' => $visibility]);
    }
}
