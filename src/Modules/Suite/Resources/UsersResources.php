<?php

declare(strict_types=1);

namespace Ometra\Apollo\Sdk\Modules\Suite\Resources;

use Ometra\Apollo\Sdk\Core\Http\ApolloHttpClient;
use Ometra\Apollo\Sdk\Modules\Suite\Resources\ApplicationsResource;

final class UsersResources
{
    public function __construct(private readonly ApolloHttpClient $client) {}

    public function applications(): ApplicationsResource
    {
        return new ApplicationsResource($this->client);
    }
    public function ensure(): mixed
    {
        return $this->client->userRequest('GET', 'users/ensure');
    }
}
