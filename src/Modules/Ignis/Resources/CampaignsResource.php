<?php

declare(strict_types=1);

namespace Ometra\Apollo\Sdk\Modules\Ignis\Resources;

use Ometra\Apollo\Sdk\Core\Http\ApolloHttpClient;

final class CampaignsResource
{
    public function __construct(private readonly ApolloHttpClient $client) {}

    public function byGroup(string $id_externalGroup): array
    {
        return $this->client->applicationRequest('GET', 'external-groups/' . $id_externalGroup . '/campaigns');
    }

    public function byExternalGroup(string $externalGroupId): array
    {
        return $this->byGroup($externalGroupId);
    }

    public function show(string $id_externalGroup): array
    {
        return $this->client->applicationRequest('GET', 'external-groups/' . $id_externalGroup . '/campaigns/' . $id_externalGroup);
    }
}
