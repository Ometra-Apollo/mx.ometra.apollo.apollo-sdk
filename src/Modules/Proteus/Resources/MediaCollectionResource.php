<?php

declare(strict_types=1);

namespace Ometra\Apollo\Sdk\Modules\Proteus\Resources;

use Ometra\Apollo\Sdk\Core\Http\ApolloHttpClient;

final class MediaCollectionResource
{
    public function __construct(private readonly ApolloHttpClient $client) {}

    /**
     * @param  array<string, mixed>  $filters
     * @return array<array-key, mixed>
     */
    public function index(array $filters = []): array
    {
        return $this->client->userRequest('GET', 'media', query: $filters);
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<array-key, mixed>
     */
    public function store(array $data): array
    {
        return $this->client->userRequest('POST', 'media', payload: $data);
    }

    public function metadata(): MediaMetadataValuesResource
    {
        return new MediaMetadataValuesResource($this->client);
    }
}
