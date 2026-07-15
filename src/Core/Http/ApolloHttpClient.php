<?php

declare(strict_types=1);

namespace Ometra\Apollo\Sdk\Core\Http;

use Illuminate\Http\Client\Response;
use Ometra\Caronte\Support\CaronteApplicationToken;
use Ometra\Caronte\Support\CaronteHttpClient;

class ApolloHttpClient extends CaronteHttpClient
{
    public function __construct(
        private readonly string $baseUrl,
        private readonly bool $asApplication = false,
    ) {}

    /**
     * @param  array<string, mixed>  $payload
     * @param  array<string, mixed>  $query
     * @return array{status: int, message: string, data: mixed, errors: array<int|string, mixed>}
     */
    public function userRequest(
        string $method,
        string $endpoint,
        array $payload = [],
        array $query = [],
    ): array {
        if ($this->asApplication) {
            return $this->applicationRequest($method, $endpoint, $payload, $query);
        }

        return parent::userRequest($method, $endpoint, $payload, $query);
    }

    /**
     * @param  array<string, mixed>  $payload
     * @param  array<string, mixed>  $query
     */
    public function userRawRequest(
        string $method,
        string $endpoint,
        array $payload = [],
        array $query = [],
    ): Response {

        if ($this->asApplication) {
            return $this->applicationRawRequest($method, $endpoint, $payload, $query);
        }

        return parent::userRawRequest($method, $endpoint, $payload, $query);
    }

    protected function getBaseUrl(): string
    {
        return $this->baseUrl;
    }

    protected function makeApplicationToken(): string
    {
        return CaronteApplicationToken::make();
    }
}
