<?php

declare(strict_types=1);

namespace Ometra\Apollo\Sdk\Modules\Proteus\Resources;

use Illuminate\Http\Client\Response;
use Ometra\Apollo\Sdk\Core\Http\ApolloHttpClient;

final class MediaResource
{
    public function __construct(
        private readonly ApolloHttpClient $client,
        private readonly string $mediaId,
    ) {}

    /** @return array<array-key, mixed> */
    public function show(): array
    {
        return $this->client->userRequest('GET', 'media/'.$this->mediaId);
    }

    /** @return array<array-key, mixed> */
    public function destroy(): array
    {
        return $this->client->userRequest('DELETE', 'media/'.$this->mediaId);
    }

    public function download(?string $extension = null): Response
    {
        return $this->client->userRawRequest(
            'GET',
            'media/'.$this->mediaId.'/download',
            query: array_filter(['ext' => $extension]),
        );
    }

    public function thumbnail(): Response
    {
        return $this->client->userRawRequest(
            'GET',
            'media/'.$this->mediaId.'/download',
            query: ['ext' => 'thumb'],
        );
    }

    /**
     * @return ($key is null ? MediaMetadataCollectionResource : MediaMetadataResource)
     */
    public function metadata(?string $key = null): MediaMetadataCollectionResource|MediaMetadataResource
    {
        return $key === null
            ? new MediaMetadataCollectionResource($this->client, $this->mediaId)
            : new MediaMetadataResource($this->client, $this->mediaId, $key);
    }

    public function lightPath(): LightPathRequestResource
    {
        return new LightPathRequestResource($this->client, $this->mediaId);
    }
}
