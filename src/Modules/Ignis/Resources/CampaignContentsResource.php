<?php

declare(strict_types=1);

namespace Ometra\Apollo\Sdk\Modules\Ignis\Resources;

use Ometra\Apollo\Sdk\Core\Http\ApolloHttpClient;

final class CampaignContentsResource
{
    public function __construct(
        private readonly ApolloHttpClient $client,
        private readonly string $groupId,
        private readonly int $campaignId,
    ) {}

    public function lightpath(): CampaignContentLightPathResource
    {
        return new CampaignContentLightPathResource($this->client, $this->groupId, $this->campaignId);
    }
}
