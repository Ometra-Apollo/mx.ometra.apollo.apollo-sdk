<?php

declare(strict_types=1);

namespace Ometra\Apollo\Sdk\Modules\Ignis\Resources;

use Ometra\Apollo\Sdk\Core\Http\ApolloHttpClient;

final class GroupResource
{
    public function __construct(
        private readonly ApolloHttpClient $client,
        private readonly string $groupId,
    ) {}

    /**
     * @return ($campaignId is null ? CampaignCollectionResource : CampaignResource)
     */
    public function campaigns(?int $campaignId = null): CampaignCollectionResource|CampaignResource
    {
        return $campaignId === null
            ? new CampaignCollectionResource($this->client, $this->groupId)
            : new CampaignResource($this->client, $this->groupId, $campaignId);
    }
}
