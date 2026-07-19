<?php

declare(strict_types=1);

namespace Ometra\Apollo\Sdk\Modules\Ignis\Resources;

use Ometra\Apollo\Sdk\Core\Http\ApolloHttpClient;
use Ometra\Apollo\Sdk\DTO\IgnisCampaignDetailDTO;
use Ometra\Apollo\Sdk\DTO\IgnisCampaignDTO;

final class CampaignsResource
{
    public function __construct(private readonly ApolloHttpClient $client) {}

    public function byGroup(string $id_externalGroup): array
    {
        return array_map(
            static fn (array $campaign): array => IgnisCampaignDTO::fromArray($campaign)->toArray(),
            $this->unwrapList(
                $this->client->applicationRequest('GET', 'external-groups/'.$id_externalGroup.'/campaigns'),
            ),
        );
    }

    public function byExternalGroup(string $id_externalGroup): array
    {
        return $this->byGroup($id_externalGroup);
    }

    public function show(string $id_externalGroup, int $id_campaign): array
    {
        return IgnisCampaignDetailDTO::fromArray(
            $this->unwrapItem(
                $this->client->applicationRequest(
                    'GET',
                    'external-groups/'.$id_externalGroup.'/campaigns/'.$id_campaign,
                ),
            ),
        )->toArray();
    }

    /**
     * @param  array{data?: mixed}  $response
     */
    private function unwrapList(array $response): array
    {
        return array_values(array_filter(is_array($response['data'] ?? null) ? $response['data'] : [], 'is_array'));
    }

    /**
     * @param  array{data?: mixed}  $response
     * @return array<string, mixed>
     */
    private function unwrapItem(array $response): array
    {
        return is_array($response['data'] ?? null) ? $response['data'] : [];
    }
}
