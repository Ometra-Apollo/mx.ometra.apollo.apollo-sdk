<?php

declare(strict_types=1);

namespace Ometra\Apollo\Sdk\Modules\Pulse\Resources;

use Ometra\Apollo\Sdk\Core\Http\ApolloHttpClient;

final class GroupCatalogResource
{
    public function __construct(private readonly ApolloHttpClient $client) {}

    /** @param array<string, mixed> $filters */
    public function index(array $filters = []): array
    {
        return $this->client->applicationRequest('GET', 'groups/catalog', query: $filters);
    }
}
