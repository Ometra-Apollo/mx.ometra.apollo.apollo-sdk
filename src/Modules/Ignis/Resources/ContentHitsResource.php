<?php

declare(strict_types=1);

namespace Ometra\Apollo\Sdk\Modules\Ignis\Resources;

use Ometra\Apollo\Sdk\Core\Http\ApolloHttpClient;

final class ContentHitsResource
{
    public function __construct(private readonly ApolloHttpClient $client)
    {
    }

    /** @param array<int, array<string, mixed>> $report */
    public function report(array $report): array
    {
        return $this->client->applicationRequest('POST', 'content-hits', payload: $report);
    }
}
