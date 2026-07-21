<?php

declare(strict_types=1);

namespace Ometra\Apollo\Sdk\Modules\Proteus\Resources;

use Ometra\Apollo\Sdk\Core\Http\ApolloHttpClient;

final class MediaMetadataResource
{
    public function __construct(
        private readonly ApolloHttpClient $client,
        private readonly string $mediaId,
        private readonly string $key,
    ) {}

    /** @return array<array-key, mixed> */
    public function show(): array
    {
        return $this->client->userRequest('GET', 'media/'.$this->mediaId.'/metadata/'.$this->key);
    }

    /** @return array<array-key, mixed> */
    public function destroy(): array
    {
        return $this->client->userRequest('DELETE', 'media/'.$this->mediaId.'/metadata/'.$this->key);
    }
}
