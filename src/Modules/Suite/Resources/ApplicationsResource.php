<?php

declare(strict_types=1);

namespace Ometra\Apollo\Sdk\Modules\Suite\Resources;

use Ometra\Apollo\Sdk\Core\Http\ApolloHttpClient;

final class ApplicationsResource
{
    public function __construct(private readonly ApolloHttpClient $client) {}

    public function user(): mixed
    {
        return $this->client->userRequest('GET', 'applications/user');
    }
}
