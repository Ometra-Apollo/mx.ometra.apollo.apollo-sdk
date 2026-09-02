<?php

declare(strict_types=1);

namespace Ometra\Apollo\Sdk\Modules\Suite\Resources;

use Ometra\Apollo\Sdk\Core\Http\ApolloHttpClient;

final class GroupsResource
{
    public function __construct(private readonly ApolloHttpClient $client) {}

    public function index(): mixed
    {
        return $this->client->userRequest('GET', 'groups');
    }

    public function effective(): mixed
    {
        return $this->client->userRequest('GET', "groups/effective");
    }

    public function show(string $id_group): mixed
    {
        return $this->client->userRequest('GET', "groups/{$id_group}");
    }

    public function create(array $data): mixed
    {
        return $this->client->userRequest('POST', 'groups', $data);
    }

    /** @param array<string, mixed> $data */
    public function update(string $id_group, array $data): mixed
    {
        return $this->client->userRequest('PUT', "groups/{$id_group}", $data);
    }
    public function delete(string $id_group): mixed
    {
        return $this->client->userRequest('DELETE', "groups/{$id_group}");
    }

}
