<?php
declare(strict_types=1);

namespace Ometra\Apollo\Sdk\Modules\Suite\Resources;

use Ometra\Apollo\Sdk\Core\Http\ApolloHttpClient;

final class ClientUsersResource
{

    protected readonly string $route_prefix;
    public function __construct(
        private readonly ApolloHttpClient $client, 
        private readonly string $id_client) {
            $this->route_prefix = "clients/{$this->id_client}/users";
        }

    public function index(): mixed
    {
        return $this->client->userRequest('GET', $this->route_prefix);
    }

    public function show(string $uri_user): mixed
    {
        return $this->client->userRequest('GET', "{$this->route_prefix}/{$uri_user}");
    }

    public function create(array $data): mixed
    {
        return $this->client->userRequest('POST', $this->route_prefix, $data);
    }

    public function update(string $uri_user, array $data): mixed
    {
        return $this->client->userRequest('PUT', "{$this->route_prefix}/{$uri_user}", $data);
    }

    public function delete(string $uri_user): mixed
    {
        return $this->client->userRequest('DELETE', "{$this->route_prefix}/{$uri_user}");
    }
}
