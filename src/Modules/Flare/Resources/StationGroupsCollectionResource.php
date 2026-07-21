<?php

declare(strict_types=1);

namespace Ometra\Apollo\Sdk\Modules\Flare\Resources;

use Ometra\Apollo\Sdk\Core\Http\ApolloHttpClient;

final class StationGroupsCollectionResource
{
    public function __construct(private readonly ApolloHttpClient $client) {}

    /** @return array<array-key, mixed> */
    public function invalidateCache(): array
    {
        return $this->client->applicationRequest('POST', 'stations/groups/cache/invalidate');
    }
}
