<?php

declare(strict_types=1);

namespace Ometra\Apollo\Sdk\Modules\Flare\Resources;

use Ometra\Apollo\Sdk\Core\Http\ApolloHttpClient;

final class StationsResource
{
    public function __construct(private readonly ApolloHttpClient $client) {}

    /** @param array<string, mixed> $query */
    public function index(array $query = []): array
    {
        return $this->client->applicationRequest('GET', 'stations', query: $query);
    }

    public function show(string $id_station, bool $nameStation = false): array
    {
        return $this->client->applicationRequest(
            'GET',
            'stations/'.$id_station,
            [],
            array_filter(['nameStation' => $nameStation])
        );
    }

    public function showByGroup(string $uri_group): array
    {
        return $this->client->applicationRequest('GET', 'stations/groups/'.$uri_group);
    }

    /** @param array<int, string> $uri_groups */
    public function assignGroups(string $id_station, array $uri_groups): array
    {
        return $this->client->applicationRequest(
            'PUT',
            'stations/'.$id_station.'/groups/assign',
            ['uri_groups' => array_values($uri_groups)],
        );
    }

    public function detachGroup(string $uri_group): array
    {
        return $this->client->applicationRequest('DELETE', 'stations/groups/'.$uri_group);
    }

    public function invalidateGroupCatalogCache(): array
    {
        return $this->client->applicationRequest('POST', 'stations/groups/cache/invalidate');
    }
}
