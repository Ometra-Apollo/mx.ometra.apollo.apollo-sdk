<?php

declare(strict_types=1);

namespace Ometra\Apollo\Sdk\Modules\Proteus\Resources;

use Ometra\Apollo\Sdk\Core\Http\ApolloHttpClient;

final class LightPathResource
{
    public function __construct(
        private readonly ApolloHttpClient $client,
        private readonly string $lightPathGrantId,
    ) {}

    public function extend(int $ttlSeconds): array
    {
        return $this->client->userRequest(
            'PATCH',
            'lightpath/grants/'.$this->lightPathGrantId.'/extend',
            payload: ['url_ttl_seconds' => $ttlSeconds],
        );
    }

    public function revoke(): array
    {
        return $this->client->userRequest(
            'DELETE',
            'lightpath/grants/'.$this->lightPathGrantId,
        );
    }
}
