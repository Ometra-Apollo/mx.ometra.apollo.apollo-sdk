<?php

declare(strict_types=1);

namespace Ometra\Apollo\Sdk\Core\Http;

use Equidna\BeeHive\Tenancy\TenantContext;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Ometra\Caronte\Exceptions\CaronteApiException;
use Ometra\Caronte\Facades\Caronte;
use Ometra\Caronte\Support\CaronteApplicationToken;
use Ometra\Caronte\Support\CaronteHttpClient;
use RuntimeException;

class ApolloHttpClient extends CaronteHttpClient
{
    public function __construct(
        private readonly string $baseUrl,
        private readonly bool $asApplication = false,
    ) {
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
    ): array {
        return $this->request($method, $endpoint, $payload, $query, $this->applicationHeaders());
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
        if ($this->asApplication) {
            return $this->applicationRequest($method, $endpoint, $payload, $query);
        }

        return $this->request($method, $endpoint, $payload, $query, $this->userHeaders());
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
        return $this->requestRaw($method, $endpoint, $payload, $query, $this->userHeaders());
    }

    protected function getBaseUrl(): string
    {
        return $this->baseUrl;
    }

    protected function makeApplicationToken(): string
    {
        return CaronteApplicationToken::make();
    }

    protected function makeGroupToken(): string
    {
        return CaronteApplicationToken::makeGroup();
    }

    /**
     * @param  array<string, mixed>  $payload
     * @param  array<string, mixed>  $query
     * @param  array<string, string>  $headers
     * @return array{status: int, message: string, data: mixed, errors: array<int|string, mixed>}
     */
    protected function request(
        string $method,
        string $endpoint,
        array $payload,
        array $query,
        array $headers,
    ): array {
        return $this->parseResponse(
            $this->requestRaw($method, $endpoint, $payload, $query, $headers, acceptJson: true)
        );
    }

    /**
     * @param  array<string, mixed>  $payload
     * @param  array<string, mixed>  $query
     * @param  array<string, string>  $headers
     */
    protected function requestRaw(
        string $method,
        string $endpoint,
        array $payload,
        array $query,
        array $headers,
        bool $acceptJson = false,
    ): Response {
        $url = rtrim($this->getBaseUrl(), '/') . '/' . ltrim($endpoint, '/');
        $urlWithQuery = $query !== [] ? $url . '?' . http_build_query($query) : $url;
        $method = strtolower($method);

        $request = $this->httpRequest($headers, $acceptJson);

        if ($this->shouldUseMultipart($payload)) {
            $request = $request->asMultipart();
            $payload = $this->toMultipart($payload);
        }

        return match ($method) {
            'get' => $request->get($url, $query),
            'delete' => $request->delete($url, $payload !== [] ? $payload : $query),
            'post' => $request->post($urlWithQuery, $payload),
            'put' => $request->put($urlWithQuery, $payload),
            'patch' => $request->patch($urlWithQuery, $payload),
            default => throw new CaronteApiException("Unsupported HTTP method [{$method}].", 500),
        };
    }

    /**
     * @return array<string, string>
     */
    protected function applicationHeaders(): array
    {
        $headers = [
            'X-Application-Token' => $this->makeApplicationToken(),
        ];

        return $this->withOptionalCaronteHeaders($headers);
    }

    /**
     * @return array<string, string>
     */
    protected function userHeaders(): array
    {
        $headers = [
            'X-Application-Token' => $this->makeApplicationToken(),
            'X-User-Token' => Caronte::getToken()->toString(),
            'Authorization' => 'Bearer ' . Caronte::getToken()->toString(),
        ];

        return $this->withOptionalCaronteHeaders($headers);
    }

    /**
     * @param  array<string, string>  $headers
     * @return array<string, string>
     */
    private function withOptionalCaronteHeaders(array $headers): array
    {
        $groupToken = $this->makeGroupToken();
        if ($groupToken !== '') {
            $headers['X-Group-Token'] = $groupToken;
        }

        $tenantId = $this->tenantId();
        if ($tenantId !== null) {
            $headers['X-Tenant-Id'] = $tenantId;
        }

        return $headers;
    }

    /**
     * @param  array<string, string>  $headers
     */
    private function httpRequest(array $headers, bool $acceptJson): PendingRequest
    {
        return Http::accept($acceptJson ? 'application/json' : '*/*')
            ->withOptions([
                'verify' => (bool) config('caronte.tls_verify', true),
            ])
            ->timeout((int) config('caronte.http.timeout', 10))
            ->retry(
                times: (int) config('caronte.http.retries', 1),
                sleepMilliseconds: (int) config('caronte.http.retry_sleep', 150)
            )
            ->withHeaders($headers);
    }

    private function tenantId(): ?string
    {
        if (! app()->bound(TenantContext::class)) {
            return null;
        }

        /** @var TenantContext $tenantContext */
        $tenantContext = app(TenantContext::class);
        $tenantId = $tenantContext->get();

        return is_string($tenantId) && trim($tenantId) !== '' ? trim($tenantId) : null;
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    private function shouldUseMultipart(array $payload): bool
    {
        foreach ($payload as $value) {
            if ($value instanceof UploadedFile || is_resource($value)) {
                return true;
            }

            if (is_array($value) && $this->shouldUseMultipart($value)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param  array<string, mixed>  $payload
     * @return array<int, array{name: string, contents: mixed, filename?: string}>
     */
    private function toMultipart(array $payload, ?string $prefix = null): array
    {
        $parts = [];

        foreach ($payload as $key => $value) {
            $name = $prefix === null ? (string) $key : "{$prefix}[{$key}]";

            if ($value instanceof UploadedFile) {
                $parts[] = $this->filePart($name, $value);
                continue;
            }

            if (is_array($value)) {
                if ($this->isListOfFiles($value)) {
                    foreach ($value as $file) {
                        $parts[] = $this->filePart($name . '[]', $file);
                    }
                    continue;
                }

                $parts = array_merge($parts, $this->toMultipart($value, $name));
                continue;
            }

            $parts[] = [
                'name' => $name,
                'contents' => $this->scalarPartContents($value),
            ];
        }

        return $parts;
    }

    /**
     * @param array<int|string, mixed> $value
     */
    private function isListOfFiles(array $value): bool
    {
        if ($value === []) {
            return false;
        }

        foreach ($value as $item) {
            if (! $item instanceof UploadedFile) {
                return false;
            }
        }

        return true;
    }

    /**
     * @return array{name: string, contents: resource, filename: string}
     */
    private function filePart(string $name, UploadedFile $file): array
    {
        $path = $file->getRealPath();

        if (! is_string($path)) {
            throw new RuntimeException("Cannot read uploaded file [{$file->getClientOriginalName()}].");
        }

        $stream = fopen($path, 'r');

        if (! is_resource($stream)) {
            throw new RuntimeException("Cannot open uploaded file [{$file->getClientOriginalName()}].");
        }

        return [
            'name' => $name,
            'contents' => $stream,
            'filename' => $file->getClientOriginalName(),
        ];
    }

    private function scalarPartContents(mixed $value): string
    {
        if (is_bool($value)) {
            return $value ? '1' : '0';
        }

        return $value === null ? '' : (string) $value;
    }
}
