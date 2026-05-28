<?php

declare(strict_types=1);

namespace Ometra\Apollo\Sdk\Modules\Pulse\Resources;

use Ometra\Apollo\Sdk\Core\Http\ApolloHttpClient;

final class GroupsResource
{
    public function __construct(private readonly ApolloHttpClient $client)
    {
    }

    /** @param array<string, mixed> $query */
    public function index(array $query = []): array
    {
        return $this->client->applicationRequest('GET', 'ignis/groups', query: $query);
    }
}
