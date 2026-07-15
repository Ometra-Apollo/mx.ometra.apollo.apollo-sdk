<?php

declare(strict_types=1);

namespace Ometra\Apollo\Sdk\Modules\Proteus\Resources;

use Ometra\Apollo\Sdk\Core\Http\ApolloHttpClient;

final class PresetsResource
{
    public function __construct(private readonly ApolloHttpClient $client)
    {
    }

    public function index(string $id_directory): array
    {
        return $this->client->userRequest('GET', 'directories/' . $id_directory . '/presets');
    }

    /** @param array<string, mixed> $data */
    public function store(string $id_directory, array $data): array
    {
        return $this->client->userRequest('POST', 'directories/' . $id_directory . '/presets', payload: $data);
    }

    public function show(string $id_directory, string $id_preset): array
    {
        return $this->client->userRequest('GET', 'directories/' . $id_directory . '/presets/' . $id_preset);
    }

    /** @param array<string, mixed> $data */
    public function update(string $id_directory, string $id_preset, array $data): array
    {
        return $this->client->userRequest('PUT', 'directories/' . $id_directory . '/presets/' . $id_preset, payload: $data);
    }

    public function delete(string $id_directory, string $id_preset): ?array
    {
        return $this->client->userRequest('DELETE', 'directories/' . $id_directory . '/presets/' . $id_preset);
    }
}
