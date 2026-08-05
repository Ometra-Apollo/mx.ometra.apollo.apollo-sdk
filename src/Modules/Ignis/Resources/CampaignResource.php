<?php

declare(strict_types=1);

namespace Ometra\Apollo\Sdk\Modules\Ignis\Resources;

use Ometra\Apollo\Sdk\Core\Http\ApolloHttpClient;

final class CampaignResource
{
    public function __construct(
        private readonly ApolloHttpClient $client,
        private readonly string $groupId,
        private readonly int $campaignId,
    ) {}

    /** @return array<array-key, mixed> */
    public function show(): array
    {
        return $this->client->applicationRequest(
            'GET',
            'groups/'.$this->groupId.'/campaigns/'.$this->campaignId,
        );
    }

    public function playlogs(): CampaignPlaylogsResource
    {
        return new CampaignPlaylogsResource($this->client, $this->groupId, $this->campaignId);
    }

    public function contents(): CampaignContentsResource
    {
        return new CampaignContentsResource($this->client, $this->groupId, $this->campaignId);
    }
}
