<?php

declare(strict_types=1);

namespace Ometra\Apollo\Sdk\Modules\Pulse\Resources;

use Ometra\Apollo\Sdk\Core\Http\ApolloHttpClient;

final class GroupStationCacheResource
{
    public function __construct(private readonly ApolloHttpClient $client) {}

    /** @param array<int, string> $groupUris */
    public function invalidate(array $groupUris): array
    {
        return $this->client->applicationRequest(
            'POST',
            'groups/station-cache/invalidate',
            ['uri_groups' => array_values($groupUris)],
        );
    }
}
