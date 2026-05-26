<?php

declare(strict_types=1);

namespace Ometra\Apollo\Sdk\Modules\Proteus\Resources;

use Ometra\Apollo\Sdk\Core\Http\ApolloHttpClient;

final class PresetsResource
{
    public function __construct(private readonly ApolloHttpClient $client)
    {
    }

    public function index(string $directoryId): array
    {
        return $this->client->userRequest('GET', 'directories/' . $directoryId . '/presets');
    }

    /** @param array<string, mixed> $data */
    public function store(string $directoryId, array $data): array
    {
        return $this->client->userRequest('POST', 'directories/' . $directoryId . '/presets', payload: $data);
    }

    public function show(string $directoryId, string $presetId): array
    {
        return $this->client->userRequest('GET', 'directories/' . $directoryId . '/presets/' . $presetId);
    }

    /** @param array<string, mixed> $data */
    public function update(string $directoryId, string $presetId, array $data): array
    {
        return $this->client->userRequest('PUT', 'directories/' . $directoryId . '/presets/' . $presetId, payload: $data);
    }

    public function delete(string $directoryId, string $presetId): ?array
    {
        return $this->client->userRequest('DELETE', 'directories/' . $directoryId . '/presets/' . $presetId);
    }
}
