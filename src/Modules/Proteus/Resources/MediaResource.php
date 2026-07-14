<?php

declare(strict_types=1);

namespace Ometra\Apollo\Sdk\Modules\Proteus\Resources;

use Illuminate\Http\Client\Response;
use Ometra\Apollo\Sdk\Core\Http\ApolloHttpClient;

final class MediaResource
{
    public function __construct(private readonly ApolloHttpClient $client)
    {
    }

    /** @param array<string, mixed> $data */
    public function index(array $data = []): array
    {
        return $this->client->userRequest('GET', 'media', query: $data);
    }

    public function create(): array
    {
        return $this->client->userRequest('GET', 'media/create');
    }

    public function tags(): array
    {
        return $this->client->userRequest('GET', 'media/tags');
    }

    public function show(string $id): array
    {
        return $this->client->userRequest('GET', 'media/' . $id);
    }

    /** @param array<string, mixed> $data */
    public function upload(array $data): array
    {
        return $this->client->userRequest('POST', 'media', payload: $data);
    }

    public function delete(string $id): ?array
    {
        return $this->client->userRequest('DELETE', 'media/' . $id);
    }

    public function availableFormats(string $id): array
    {
        return $this->client->userRequest('GET', 'media/' . $id . '/available-formats');
    }

    /** @param array<string, mixed> $data */
    public function setDefaultFormat(string $id, array $data): array
    {
        return $this->client->userRequest('POST', 'media/' . $id . '/available-formats', payload: $data);
    }

    public function transformationOptions(string $id): array
    {
        return $this->client->userRequest('GET', 'media/' . $id . '/request-transformations');
    }

    /** @param array<string, mixed> $data */
    public function requestTransformations(string $id, array $data): array
    {
        return $this->client->userRequest('POST', 'media/' . $id . '/request-transformations', payload: $data);
    }

    /** @param array<string, mixed> $data */
    public function setMetadata(string $id, array $data): array
    {
        return $this->client->userRequest('POST', 'media/' . $id . '/set-metadata', payload: $data);
    }

    /** @param array<string, string> $visibility ('published', 'protected') */
    public function setVisibility(string $id, bool $visibility): array
    {
        return $this->client->userRequest('POST', 'media/' . $id . '/set-visibility', payload: ['visibility' => $visibility]);
    }

    /** @param array<string, mixed> $data */
    public function storeTags(string $id, array $data): array
    {
        return $this->client->userRequest('POST', 'media/' . $id . '/tags/store', payload: $data);
    }

    public function download(string $id, ?string $ext = null): Response
    {
        return $this->client->userRawRequest(
            'GET',
            'media/' . $id . '/download',
            query: array_filter(['ext' => $ext]),
        );
    }

     public function thumbnail(string $id): Response
    {
        return $this->client->userRawRequest(
            'GET',
            'media/' . $id . '/download',
            query: array_filter(['ext' => 'thumb']),
        );
    }

    public function saveLocal(string $id, string $ext): Response
    {
        return $this->download($id, $ext);
    }

    /** @param array<string, mixed> $options */
    public function lightPathUrl(string $id, array $options = []): array
    {
        return $this->client->userRequest(
            'POST',
            'media/' . $id . '/lightpath-url',
            payload: $options,
        );
    }
}
