<?php

declare(strict_types=1);

namespace Ometra\Apollo\Sdk\Modules\Flare\Resources;

use Ometra\Apollo\Sdk\Core\Http\ApolloHttpClient;

final class StationsResource
{
    public function __construct(private readonly ApolloHttpClient $client) {}

    /**
     * @return ($groupUri is null ? StationGroupsCollectionResource : StationGroupResource)
     */
    public function groups(?string $groupUri = null): StationGroupsCollectionResource|StationGroupResource
    {
        return $groupUri === null
            ? new StationGroupsCollectionResource($this->client)
            : new StationGroupResource($this->client, $groupUri);
    }
}
