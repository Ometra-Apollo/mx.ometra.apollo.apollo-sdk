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

    /** @return array<array-key, mixed> */
    public function request(
        string $clientReference,
        DirectoryApplicationPermission $permission,
        ?string $targetApplicationId = null,
    ): array {
        return $this->client->userRequest(
            'POST',
            'directories/'.$this->directoryId.'/application-grants',
            payload: array_filter([
                'client_reference' => $clientReference,
                'permission' => $permission->value,
                'target_application_id' => $targetApplicationId,
            ], static fn (mixed $value): bool => $value !== null),
        );
    }
}
