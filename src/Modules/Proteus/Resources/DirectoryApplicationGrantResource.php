<?php

declare(strict_types=1);

namespace Ometra\Apollo\Sdk\Modules\Proteus\Resources;

use Ometra\Apollo\Sdk\Core\Http\ApolloHttpClient;

final class DirectoryApplicationGrantResource
{
    public function __construct(
        private readonly ApolloHttpClient $client,
        private readonly string $applicationGrantId,
    ) {}

    /** @return array<array-key, mixed> */
    public function revoke(): array
    {
        return $this->client->applicationRequest(
            'DELETE',
            'directories/application-grants/'.$this->applicationGrantId,
        );
    }
}
