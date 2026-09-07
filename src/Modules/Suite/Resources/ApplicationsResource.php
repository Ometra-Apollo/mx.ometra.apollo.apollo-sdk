<?php

declare(strict_types=1);

namespace Ometra\Apollo\Sdk\Modules\Suite\Resources;

use Ometra\Apollo\Sdk\Core\Http\ApolloHttpClient;

final class ApplicationsResource
{
    public function __construct(private readonly ApolloHttpClient $client) {}

    public function index(): mixed
    {
        return $this->client->userRequest('GET', 'users/applications');
    }

    public function recoverPassword(string $email): mixed
    {
        return $this->client->userRequest('POST', 'caronte/auth/password/recover', ['email' => $email]);
    }

    public function passwordRecoverTokenValidation(string $token): mixed
    {
        return $this->client->userRequest('GET', "caronte/auth/password/recover/{$token}");
    }

    public function passwordRecover(string $token, array $data): mixed
    {
        return $this->client->userRequest('POST', "caronte/auth/password/recover/{$token}", $data);
    }
}
