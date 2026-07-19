<?php

declare(strict_types=1);

namespace Ometra\Apollo\Sdk\Modules\Pulse\Resources;

use Ometra\Apollo\Sdk\Core\Http\ApolloHttpClient;

final class GroupsResource
{
    public function __construct(private readonly ApolloHttpClient $client) {}

    /** @param array<string, mixed> $query */
    public function index(array $query = []): array
    {
        return $this->client->applicationRequest('GET', 'ignis/groups', query: $query);
    }

    /** @param array<string, mixed> $query */
    public function catalog(array $query = []): array
    {
        return $this->client->applicationRequest('GET', 'groups/catalog', query: $query);
    }

    /** @param array<int, string> $uri_groups */
    public function invalidateStationCache(array $uri_groups): array
    {
        return $this->client->applicationRequest(
            'POST',
            'groups/station-cache/invalidate',
            ['uri_groups' => array_values($uri_groups)],
        );
    }
}
