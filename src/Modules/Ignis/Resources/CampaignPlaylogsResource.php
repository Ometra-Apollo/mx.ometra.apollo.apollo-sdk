<?php

declare(strict_types=1);

namespace Ometra\Apollo\Sdk\Modules\Ignis\Resources;

use Ometra\Apollo\Sdk\Core\Http\ApolloHttpClient;

final class CampaignPlaylogsResource
{
    public function __construct(
        private readonly ApolloHttpClient $client,
        private readonly string $groupId,
        private readonly int $campaignId,
    ) {}

    /**
     * @param  array<int, array<string, mixed>>  $playlogs
     * @return array<array-key, mixed>
     */
    public function store(array $playlogs): array
    {
        return $this->client->applicationRequest(
            'POST',
            'groups/'.$this->groupId.'/campaigns/'.$this->campaignId.'/playlogs',
            ['playlogs' => $playlogs],
        );
    }
}
