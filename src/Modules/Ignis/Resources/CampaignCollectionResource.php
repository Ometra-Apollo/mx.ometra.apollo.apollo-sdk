<?php

declare(strict_types=1);

namespace Ometra\Apollo\Sdk\Modules\Ignis\Resources;

use Ometra\Apollo\Sdk\Core\Http\ApolloHttpClient;

final class CampaignCollectionResource
{
    public function __construct(
        private readonly ApolloHttpClient $client,
        private readonly string $externalGroupId,
    ) {}

    public function index(): array
    {
        return $this->client->applicationRequest(
            'GET',
            'external-groups/'.$this->externalGroupId.'/campaigns',
        );
    }
}
