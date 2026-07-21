<?php

declare(strict_types=1);

namespace Ometra\Apollo\Sdk\Modules\Proteus\Resources;

use Ometra\Apollo\Sdk\Core\Http\ApolloHttpClient;

final class MediaMetadataValuesResource
{
    public function __construct(private readonly ApolloHttpClient $client) {}

    /** @return array<array-key, mixed> */
    public function values(string $key): array
    {
        return $this->client->applicationRequest('GET', 'media/metadata/'.$key.'/values');
    }
}
