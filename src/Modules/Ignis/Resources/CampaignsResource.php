<?php

declare(strict_types=1);

namespace Ometra\Apollo\Sdk\Modules\Ignis\Resources;

use Ometra\Apollo\Sdk\Core\Http\ApolloHttpClient;

final class CampaignsResource
{
    public function __construct(private readonly ApolloHttpClient $client) {}

    public function byGroup(string $externalGroupId): array
    {
        return $this->client->applicationRequest('GET', 'external-groups/' . $externalGroupId . '/campaigns');
    }
}
