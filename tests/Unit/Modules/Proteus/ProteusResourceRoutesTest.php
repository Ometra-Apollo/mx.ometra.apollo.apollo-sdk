<?php

declare(strict_types=1);

use Ometra\Apollo\Sdk\Modules\Proteus\Enums\DirectoryApplicationPermission;
use Ometra\Apollo\Sdk\Modules\Proteus\Resources\CategoriesResource;
use Ometra\Apollo\Sdk\Modules\Proteus\Resources\DirectoriesCollectionResource;
use Ometra\Apollo\Sdk\Modules\Proteus\Resources\DirectoryApplicationGrantRequestResource;
use Ometra\Apollo\Sdk\Modules\Proteus\Resources\DirectoryApplicationGrantResource;
use Ometra\Apollo\Sdk\Modules\Proteus\Resources\DirectoryResource;
use Ometra\Apollo\Sdk\Modules\Proteus\Resources\LightPathRequestResource;
use Ometra\Apollo\Sdk\Modules\Proteus\Resources\LightPathResource;
use Ometra\Apollo\Sdk\Modules\Proteus\Resources\MediaCollectionResource;
use Ometra\Apollo\Sdk\Modules\Proteus\Resources\MediaMetadataCollectionResource;
use Ometra\Apollo\Sdk\Modules\Proteus\Resources\MediaMetadataResource;
use Ometra\Apollo\Sdk\Modules\Proteus\Resources\MediaMetadataValuesResource;
use Ometra\Apollo\Sdk\Modules\Proteus\Resources\MediaResource;
use PHPUnit\Framework\TestCase;

require_once __DIR__.'/RecordingApolloHttpClient.php';

final class ProteusResourceRoutesTest extends TestCase
{
    public function test_media_collection_routes(): void
    {
        $client = new RecordingApolloHttpClient;
        $media = new MediaCollectionResource($client);

        $media->index(['type' => 'image']);
        $this->assertRequest($client, 'user', 'GET', 'media', query: ['type' => 'image']);

        $media->store(['type' => 'image']);
        $this->assertRequest($client, 'user', 'POST', 'media', payload: ['type' => 'image']);
    }

    public function test_bound_media_routes(): void
    {
        $client = new RecordingApolloHttpClient;
        $media = new MediaResource($client, 'media-1');

        $media->show();
        $this->assertRequest($client, 'user', 'GET', 'media/media-1');

        $media->destroy();
        $this->assertRequest($client, 'user', 'DELETE', 'media/media-1');

        $media->download('jpg');
        $this->assertRequest($client, 'user', 'GET', 'media/media-1/download', query: ['ext' => 'jpg'], raw: true);

        $media->preview();
        $this->assertRequest($client, 'user', 'GET', 'media/media-1/download', query: ['ext' => 'preview'], raw: true);
    }

    public function test_nested_metadata_routes(): void
    {
        $client = new RecordingApolloHttpClient;

        (new MediaMetadataValuesResource($client))->values('author');
        $this->assertRequest($client, 'application', 'GET', 'media/metadata/author/values');

        $metadata = new MediaMetadataCollectionResource($client, 'media-1');
        $metadata->store(['metadata' => ['author' => 'Ada']]);
        $this->assertRequest($client, 'user', 'POST', 'media/media-1/metadata', payload: ['metadata' => ['author' => 'Ada']]);

        $metadata->update(['metadata' => [['key' => 'author', 'value' => 'Ada']]]);
        $this->assertRequest($client, 'user', 'PUT', 'media/media-1/metadata', payload: ['metadata' => [['key' => 'author', 'value' => 'Ada']]]);

        $item = new MediaMetadataResource($client, 'media-1', 'author');
        $item->show();
        $this->assertRequest($client, 'user', 'GET', 'media/media-1/metadata/author');

        $item->destroy();
        $this->assertRequest($client, 'user', 'DELETE', 'media/media-1/metadata/author');
    }

    public function test_light_path_routes_use_domain_verbs(): void
    {
        $client = new RecordingApolloHttpClient;

        (new LightPathRequestResource($client, 'media-1'))->request('mp4', 3600);
        $this->assertRequest($client, 'user', 'POST', 'media/media-1/lightpath-url', payload: [
            'ext' => 'mp4',
            'url_ttl_seconds' => 3600,
        ]);

        $grant = new LightPathResource($client, 'grant-1');
        $grant->extend(7200);
        $this->assertRequest($client, 'application', 'PATCH', 'lightpath/grants/grant-1/extend', payload: ['url_ttl_seconds' => 7200]);

        $grant->revoke();
        $this->assertRequest($client, 'application', 'DELETE', 'lightpath/grants/grant-1');
    }

    public function test_directory_and_application_grant_routes(): void
    {
        $client = new RecordingApolloHttpClient;
        $directories = new DirectoriesCollectionResource($client);

        $directories->index(['page' => 2]);
        $this->assertRequest($client, 'user', 'GET', 'directories', query: ['page' => 2]);

        $directories->store(['name' => 'Assets']);
        $this->assertRequest($client, 'user', 'POST', 'directories', payload: ['name' => 'Assets']);

        $directory = new DirectoryResource($client, 'dir-1');
        $directory->show();
        $this->assertRequest($client, 'user', 'GET', 'directories/dir-1');

        $directory->destroy();
        $this->assertRequest($client, 'user', 'DELETE', 'directories/dir-1');

        (new DirectoryApplicationGrantRequestResource($client, 'dir-1'))->request(
            'flare:playlist:1',
            DirectoryApplicationPermission::READ,
        );
        $this->assertRequest($client, 'user', 'POST', 'directories/dir-1/application-grants', payload: [
            'client_reference' => 'flare:playlist:1',
            'permission' => 'read',
        ]);

        (new DirectoryApplicationGrantRequestResource($client, 'dir-1'))->request(
            'ignis:evidence:1', DirectoryApplicationPermission::WRITE, 'mx.ometra.apollo.lume',
        );
        $this->assertRequest($client, 'user', 'POST', 'directories/dir-1/application-grants', payload: [
            'client_reference' => 'ignis:evidence:1', 'permission' => 'write',
            'target_application_id' => 'mx.ometra.apollo.lume',
        ]);

        (new DirectoryApplicationGrantResource($client, 'grant-1'))->revoke();
        $this->assertRequest($client, 'application', 'DELETE', 'directories/application-grants/grant-1');
    }

    public function test_categories_use_canonical_routes(): void
    {
        $client = new RecordingApolloHttpClient;
        $categories = new CategoriesResource($client);

        $categories->index(['filter' => 'image']);
        $this->assertRequest($client, 'application', 'GET', 'categories', query: ['filter' => 'image']);

        $categories->store(['name' => 'Images']);
        $this->assertRequest($client, 'application', 'POST', 'categories', payload: ['name' => 'Images']);
    }

    /** @param array<string, mixed> $payload @param array<string, mixed> $query */
    private function assertRequest(
        RecordingApolloHttpClient $client,
        string $auth,
        string $method,
        string $endpoint,
        array $payload = [],
        array $query = [],
        bool $raw = false,
    ): void {
        self::assertSame(compact('auth', 'method', 'endpoint', 'payload', 'query', 'raw'), $client->lastRequest);
    }
}
