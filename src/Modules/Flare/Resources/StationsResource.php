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
            'stations/' . $id_station,
            [],
            array_filter(['nameStation' => $nameStation])
        );
    }
}
