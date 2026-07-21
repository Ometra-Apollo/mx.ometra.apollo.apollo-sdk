<?php

declare(strict_types=1);

namespace Ometra\Apollo\Sdk\Modules\Flare\Resources;

use Ometra\Apollo\Sdk\Core\Http\ApolloHttpClient;

final class StationGroupResource
{
    public function __construct(
        private readonly ApolloHttpClient $client,
        private readonly string $groupUri,
    ) {}

    public function show(): array
    {
        return $this->client->applicationRequest('GET', 'stations/groups/'.$this->groupUri);
    }

    public function destroy(): array
    {
        return $this->client->applicationRequest('DELETE', 'stations/groups/'.$this->groupUri);
    }
}
