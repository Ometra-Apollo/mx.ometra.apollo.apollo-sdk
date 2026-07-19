<?php

declare(strict_types=1);

namespace Ometra\Apollo\Sdk\Modules\Proteus\Resources;

use Ometra\Apollo\Sdk\Core\Http\ApolloHttpClient;

final class LightPathResource
{
    public function __construct(private readonly ApolloHttpClient $client)
    {
        //
    }

    public function extendGrant(string $id_lightpath_grant, int $url_ttl_seconds): array
    {
        return $this->client->userRequest(
            'PATCH',
            'lightpath/grants/'.$id_lightpath_grant.'/extend',
            payload: ['url_ttl_seconds' => $url_ttl_seconds],
        );
    }

    public function deleteGrant(string $id_lightpath_grant): ?array
    {
        return $this->client->userRequest(
            'DELETE',
            'lightpath/grants/'.$id_lightpath_grant,
        );
    }
}
