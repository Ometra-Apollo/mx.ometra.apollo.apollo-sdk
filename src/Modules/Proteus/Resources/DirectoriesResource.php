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

    public function create(?string $id_parent = null): array
    {
        $endpoint = 'directories/create';

        if ($id_parent !== null && $id_parent !== '') {
            $endpoint .= '/' . $id_parent;
        }

        return $this->client->userRequest('GET', $endpoint);
    }

    /** @param array<string, mixed> $data */
    public function store(array $data): array
    {
        return $this->client->userRequest('POST', 'directories', payload: $data);
    }

    public function show(string $id_directory): array
    {
        return $this->client->userRequest('GET', 'directories/' . $id_directory);
    }

    /** @param array<string, mixed> $data */
    public function update(string $id_directory, array $data): array
    {
        return $this->client->userRequest('PUT', 'directories/' . $id_directory, payload: $data);
    }

    public function delete(string $id_directory): ?array
    {
        return $this->client->userRequest('DELETE', 'directories/' . $id_directory);
    }

    public function setVisibility(string $id_directory, string $visibility): array
    {
        return $this->client->userRequest('POST', 'directories/' . $id_directory . '/set-visibility', payload: ['visibility' => $visibility]);
    }
}
