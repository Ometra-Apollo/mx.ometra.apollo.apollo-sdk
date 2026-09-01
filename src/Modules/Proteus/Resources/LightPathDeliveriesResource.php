<?php

declare(strict_types=1);

namespace Ometra\Apollo\Sdk\Modules\Proteus\Resources;

use Ometra\Apollo\Sdk\Core\Http\ApolloHttpClient;

final class LightPathDeliveriesResource
{
    public function __construct(private readonly ApolloHttpClient $client) {}

    /** @param array{consumer_type: string, consumer_key: string, media_id: string, format: string, ttl_seconds?: int} $delivery */
    public function register(array $delivery): array
    {
        return $this->client->applicationRequest('POST', 'lightpath/deliveries', payload: $delivery);
    }

    /** @param array<int, array{delivery_id: string, ttl_seconds?: int, force_refresh?: bool}> $deliveries */
    public function resolveBatch(array $deliveries): array
    {
        return $this->client->applicationRequest('POST', 'lightpath/deliveries/resolve-batch', payload: ['deliveries' => $deliveries]);
    }

    public function delivery(string $deliveryId): LightPathDeliveryResource
    {
        return new LightPathDeliveryResource($this->client, $deliveryId);
    }
}
