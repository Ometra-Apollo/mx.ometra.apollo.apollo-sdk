<?php

declare(strict_types=1);

namespace Ometra\Apollo\Sdk\Modules\Proteus\Resources;

use Ometra\Apollo\Sdk\Core\Http\ApolloHttpClient;

final class LightPathRequestResource
{
    public function __construct(
        private readonly ApolloHttpClient $client,
        private readonly string $mediaId,
    ) {}

    /** @return array<array-key, mixed> */
    public function request(?string $extension = null, ?int $ttlSeconds = null): array
    {
        return $this->client->userRequest(
            'POST',
            'media/'.$this->mediaId.'/lightpath-url',
            payload: array_filter([
                'ext' => $extension,
                'url_ttl_seconds' => $ttlSeconds,
            ], static fn (mixed $value): bool => $value !== null),
        );
    }
}
