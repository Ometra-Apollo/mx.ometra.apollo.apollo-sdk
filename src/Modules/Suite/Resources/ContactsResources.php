<?php

declare(strict_types=1);

namespace Ometra\Apollo\Sdk\Modules\Suite\Resources;

use Ometra\Apollo\Sdk\Core\Http\ApolloHttpClient;

final class ContactsResource
{
    protected readonly string $route_prefix;

    public function __construct(private readonly ApolloHttpClient $client,
        private readonly string $id_client)
    {
        $this->route_prefix = "clients/{$this->id_client}/contacts";
    }

    public function index(): mixed
    {
        return $this->client->userRequest('GET', $this->route_prefix);
    }

    public function show(string $id_contact): mixed
    {
        return $this->client->userRequest('GET', "{$this->route_prefix}/{$id_contact}");
    }

    /** @param array<string, mixed> $data */
    public function create(array $data): mixed
    {
        return $this->client->userRequest('POST', $this->route_prefix, $data);
    }

    /** @param array<string, mixed> $data */
    public function update(string $id_contact, array $data): mixed
    {
        return $this->client->userRequest('PUT', "{$this->route_prefix}/{$id_contact}", $data);
    }

    public function delete(string $id_contact): mixed
    {
        return $this->client->userRequest('DELETE', "{$this->route_prefix}/{$id_contact}");
    }
}
