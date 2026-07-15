<?php

declare(strict_types=1);

namespace Ometra\Apollo\Sdk\Modules\Proteus\Resources;

use Illuminate\Http\Client\Response;
use Ometra\Apollo\Sdk\Core\Http\ApolloHttpClient;

final class MediaResource
{
    public function __construct(private readonly ApolloHttpClient $client)
    {
        //
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

    public function show(string $id_media): array
    {
        return $this->client->userRequest('GET', 'media/' . $id_media);
    }

    /** @param array<string, mixed> $data */
    public function upload(array $data): array
    {
        return $this->client->userRequest('POST', 'media', payload: $data);
    }

    public function delete(string $id_media): ?array
    {
        return $this->client->userRequest('DELETE', 'media/' . $id_media);
    }

    public function availableFormats(string $id_media): array
    {
        return $this->client->userRequest('GET', 'media/' . $id_media . '/available-formats');
    }

    /** @param array<string, mixed> $data */
    public function setDefaultFormat(string $id_media, array $data): array
    {
        return $this->client->userRequest('POST', 'media/' . $id_media . '/available-formats', payload: $data);
    }

    public function transformationOptions(string $id_media): array
    {
        return $this->client->userRequest('GET', 'media/' . $id_media . '/request-transformations');
    }

    /** @param array<string, mixed> $data */
    public function requestTransformations(string $id_media, array $data): array
    {
        return $this->client->userRequest('POST', 'media/' . $id_media . '/request-transformations', payload: $data);
    }

    /** @param array<string, mixed> $data */
    public function setMetadata(string $id_media, array $data): array
    {
        return $this->client->userRequest('POST', 'media/' . $id_media . '/set-metadata', payload: $data);
    }

    /** @param array<string, string> $visibility ('published', 'protected') */
    public function setVisibility(string $id_media, bool $visibility): array
    {
        return $this->client->userRequest('POST', 'media/' . $id_media . '/set-visibility', payload: ['visibility' => $visibility]);
    }

    /** @param array<string, mixed> $data */
    public function storeTags(string $id_media, array $data): array
    {
        return $this->client->userRequest('POST', 'media/' . $id_media . '/tags/store', payload: $data);
    }

    public function download(string $id_media, ?string $ext = null): Response
    {
        return $this->client->userRawRequest(
            'GET',
            'media/' . $id_media . '/download',
            query: array_filter(['ext' => $ext]),
        );
    }

    public function thumbnail(string $id_media): Response
    {
        return $this->client->userRawRequest(
            'GET',
            'media/' . $id_media . '/download',
            query: array_filter(['ext' => 'thumb']),
        );
    }

    public function saveLocal(string $id_media, string $ext): Response
    {
        return $this->download($id_media, $ext);
    }

    /** @param array<string, mixed> $options */
    public function lightPathUrl(string $id_media, array $options = []): array
    {
        return $this->client->userRequest(
            'POST',
            'media/' . $id_media . '/lightpath-url',
            payload: $options,
        );
    }
}
