<?php

declare(strict_types=1);

namespace Ometra\Apollo\Sdk\Modules\Proteus\Resources;

use Ometra\Apollo\Sdk\Core\Http\ApolloHttpClient;
use Ometra\Apollo\Sdk\Modules\Proteus\Enums\DirectoryApplicationPermission;

final class DirectoryApplicationGrantRequestResource
{
    public function __construct(
        private readonly ApolloHttpClient $client,
        private readonly string $directoryId,
    ) {}

    public function request(
        string $clientReference,
        DirectoryApplicationPermission $permission,
    ): array {
        return $this->client->userRequest(
            'POST',
            'directories/'.$this->directoryId.'/application-grants',
            payload: [
                'client_reference' => $clientReference,
                'permission' => $permission->value,
            ],
        );
    }
}
