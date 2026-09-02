<?php

declare(strict_types=1);

namespace Ometra\Apollo\Sdk\Modules\Proteus\Resources;

use Ometra\Apollo\Sdk\Core\Http\ApolloHttpClient;
use Ometra\Apollo\Sdk\Modules\Proteus\Values\LightPathRecoveryResult;

final class LightPathDeliveryResource
{
    public function __construct(private readonly ApolloHttpClient $client, private readonly string $deliveryId) {}

    /** @return array<array-key, mixed> */
    public function refresh(?int $ttlSeconds = null): array
    {
        return $this->client->applicationRequest(
            'POST',
            'lightpath/deliveries/'.rawurlencode($this->deliveryId).'/refresh',
            payload: array_filter(['ttl_seconds' => $ttlSeconds], static fn (mixed $value): bool => $value !== null),
        );
    }

    /** @return array<array-key, mixed> */
    public function retire(): array
    {
        return $this->client->applicationRequest('DELETE', 'lightpath/deliveries/'.rawurlencode($this->deliveryId));
    }

    /** @param array{version:int,error_code:string,http_status?:int|null,source:string,consumer_key?:string|null,url_hash?:string|null} $failure */
    public function reportFailure(array $failure): LightPathRecoveryResult
    {
        return LightPathRecoveryResult::fromResponse($this->client->applicationRequest(
            'POST',
            'lightpath/deliveries/'.rawurlencode($this->deliveryId).'/failures',
            payload: $failure,
        ));
    }
}
