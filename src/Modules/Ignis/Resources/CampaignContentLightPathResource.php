<?php

declare(strict_types=1);

namespace Ometra\Apollo\Sdk\Modules\Ignis\Resources;

use Ometra\Apollo\Sdk\Core\Http\ApolloHttpClient;

final class CampaignContentLightPathResource
{
    public function __construct(
        private readonly ApolloHttpClient $client,
        private readonly string $groupId,
        private readonly int $campaignId,
    ) {}

    /** @return array<array-key, mixed> */
    public function refresh(string $mediaId): array
    {
        return $this->client->applicationRequest(
            'POST',
            'groups/'.rawurlencode($this->groupId).'/campaigns/'.$this->campaignId.'/contents/'.rawurlencode($mediaId).'/refresh-lightpath',
        );
    }
}
