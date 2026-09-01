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

    /** @return array<array-key, mixed> */
    public function extend(int $ttlSeconds): array
    {
        // Deprecated: delivery lifecycle belongs to Proteus; retained only for the rollback window.
        return $this->client->applicationRequest(
            'PATCH',
            'lightpath/grants/'.$this->lightPathGrantId.'/extend',
            payload: ['url_ttl_seconds' => $ttlSeconds],
        );
    }

    /** @return array<array-key, mixed> */
    public function revoke(): array
    {
        // Deprecated: use LightPathDeliveryResource::retire().
        return $this->client->applicationRequest(
            'DELETE',
            'lightpath/grants/'.$this->lightPathGrantId,
        );
    }
}
