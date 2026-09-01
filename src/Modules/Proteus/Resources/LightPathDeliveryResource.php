<?php

declare(strict_types=1);

namespace Ometra\Apollo\Sdk\Modules\Proteus\Resources;

use Ometra\Apollo\Sdk\Core\Http\ApolloHttpClient;

final class LightPathDeliveryResource
{
    public function __construct(private readonly ApolloHttpClient $client, private readonly string $deliveryId) {}

    public function refresh(?int $ttlSeconds = null): array
    {
        return $this->client->applicationRequest(
            'POST',
            'lightpath/deliveries/'.rawurlencode($this->deliveryId).'/refresh',
            payload: array_filter(['ttl_seconds' => $ttlSeconds], static fn (mixed $value): bool => $value !== null),
        );
    }

    public function retire(): array
    {
        return $this->client->applicationRequest('DELETE', 'lightpath/deliveries/'.rawurlencode($this->deliveryId));
    }
}
