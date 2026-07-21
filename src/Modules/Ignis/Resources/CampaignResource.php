<?php

declare(strict_types=1);

namespace Ometra\Apollo\Sdk\Modules\Ignis\Resources;

use Ometra\Apollo\Sdk\Core\Http\ApolloHttpClient;

final class CampaignResource
{
    public function __construct(
        private readonly ApolloHttpClient $client,
        private readonly string $externalGroupId,
        private readonly int $campaignId,
    ) {}

    /** @return array<array-key, mixed> */
    public function show(): array
    {
        return $this->client->applicationRequest(
            'GET',
            'external-groups/'.$this->externalGroupId.'/campaigns/'.$this->campaignId,
        );
    }
}
