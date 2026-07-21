<?php

declare(strict_types=1);

namespace Ometra\Apollo\Sdk\Modules\Proteus\Resources;

use Ometra\Apollo\Sdk\Core\Http\ApolloHttpClient;

final class MediaMetadataCollectionResource
{
    public function __construct(
        private readonly ApolloHttpClient $client,
        private readonly string $mediaId,
    ) {}

    /** @param array<string, mixed> $data */
    public function store(array $data): array
    {
        return $this->client->userRequest('POST', 'media/'.$this->mediaId.'/metadata', payload: $data);
    }

    /** @param array<string, mixed> $data */
    public function update(array $data): array
    {
        return $this->client->userRequest('PUT', 'media/'.$this->mediaId.'/metadata', payload: $data);
    }
}
