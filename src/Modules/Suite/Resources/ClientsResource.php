<?php

declare(strict_types=1);

namespace Ometra\Apollo\Sdk\Modules\Suite\Resources;

use Ometra\Apollo\Sdk\Core\Http\ApolloHttpClient;

final class ClientsResource
{
    public function __construct(private readonly ApolloHttpClient $client) {}

    public function index(): mixed
    {
        return $this->client->userRequest('GET', 'clients');
    }

    public function show(string $id_client): mixed
    {
        return $this->client->userRequest('GET', "clients/{$id_client}");
    }

    public function create(array $data): mixed
    {
        return $this->client->userRequest('POST', 'clients', $data);
    }

    public function update(string $id_client, array $data): mixed
    {
        return $this->client->userRequest('PUT', "clients/{$id_client}", $data);
    }

    public function delete(string $id_client): mixed
    {
        return $this->client->userRequest('DELETE', "clients/{$id_client}");
    }

    public function contacts(string $id_client): ContactsResource
    {
        return new ContactsResource($this->client, $id_client);
    }

    public function users(string $id_client): ClientUsersResource
    {
        return new ClientUsersResource($this->client, $id_client);
    }
}
