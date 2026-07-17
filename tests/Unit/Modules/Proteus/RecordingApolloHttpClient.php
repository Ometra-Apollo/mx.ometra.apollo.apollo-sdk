<?php

declare(strict_types=1);

use GuzzleHttp\Psr7\Response as PsrResponse;
use Illuminate\Http\Client\Response;
use Ometra\Apollo\Sdk\Core\Http\ApolloHttpClient;

final class RecordingApolloHttpClient extends ApolloHttpClient
{
    /**
     * @var array{auth: string, method: string, endpoint: string, payload: array<string, mixed>, query: array<string, mixed>, raw: bool}|null
     */
    public ?array $lastRequest = null;

    public ?string $lastUserToken = null;

    /** @param array<string, mixed>|array<int, array<string, mixed>> $responseData */
    public function __construct(private readonly array $responseData = [])
    {
        parent::__construct('https://proteus.test/api');
    }

    /**
     * @param  array<string, mixed>  $payload
     * @param  array<string, mixed>  $query
     * @return array{status: int, message: string, data: mixed, errors: array<int|string, mixed>}
     */
    public function applicationRequest(
        string $method,
        string $endpoint,
        array $payload = [],
        array $query = [],
        ?string $userToken = null,
    ): array {
        $this->lastUserToken = $userToken;

        return $this->record('application', $method, $endpoint, $payload, $query, raw: false);
    }

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
        return $this->record('user', $method, $endpoint, $payload, $query, raw: false);
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
        $this->record('user', $method, $endpoint, $payload, $query, raw: true);

        return new Response(new PsrResponse(200));
    }

    /**
     * @param  array<string, mixed>  $payload
     * @param  array<string, mixed>  $query
     * @return array{status: int, message: string, data: mixed, errors: array<int|string, mixed>}
     */
    private function record(
        string $auth,
        string $method,
        string $endpoint,
        array $payload,
        array $query,
        bool $raw,
    ): array {
        $this->lastRequest = compact('auth', 'method', 'endpoint', 'payload', 'query', 'raw');

        return [
            'status' => 200,
            'message' => 'ok',
            'data' => $this->responseData,
            'errors' => [],
        ];
    }
}
