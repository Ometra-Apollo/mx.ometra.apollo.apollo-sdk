<?php

declare(strict_types=1);

namespace Ometra\Apollo\Sdk\Modules\Suite\Resources;

use Ometra\Apollo\Sdk\Core\Http\ApolloHttpClient;
use Ometra\Apollo\Sdk\Modules\Suite\Resources\UsersResources;

final class ApplicationsResource
{
    public function __construct(private readonly ApolloHttpClient $client) {}

    public function index(): mixed
    {
        return $this->client->userRequest('GET', 'users/applications');
    }
}
