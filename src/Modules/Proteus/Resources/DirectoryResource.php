<?php

declare(strict_types=1);

namespace Ometra\Apollo\Sdk\Modules\Proteus\Resources;

use Ometra\Apollo\Sdk\Core\Http\ApolloHttpClient;

final class DirectoryResource
{
    public function __construct(
        private readonly ApolloHttpClient $client,
        private readonly string $directoryId,
    ) {}

    public function show(): array
    {
        return $this->client->userRequest('GET', 'directories/'.$this->directoryId);
    }

    public function destroy(): array
    {
        return $this->client->userRequest('DELETE', 'directories/'.$this->directoryId);
    }

    public function applicationGrants(): DirectoryApplicationGrantRequestResource
    {
        return new DirectoryApplicationGrantRequestResource($this->client, $this->directoryId);
    }
}
