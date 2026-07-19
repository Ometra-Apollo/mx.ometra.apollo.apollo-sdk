<?php

declare(strict_types=1);

use Ometra\Apollo\Sdk\Modules\Proteus\Enums\DirectoryApplicationPermission;
use Ometra\Apollo\Sdk\Modules\Proteus\Resources\CategoriesResource;
use Ometra\Apollo\Sdk\Modules\Proteus\Resources\DirectoriesResource;
use Ometra\Apollo\Sdk\Modules\Proteus\Resources\LightPathResource;
use Ometra\Apollo\Sdk\Modules\Proteus\Resources\MediaResource;
use Ometra\Apollo\Sdk\Modules\Proteus\Resources\MetadataResource;
use Ometra\Apollo\Sdk\Modules\Proteus\Resources\PresetsResource;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

require_once __DIR__.'/RecordingApolloHttpClient.php';

final class ProteusResourceRoutesTest extends TestCase
{
    public function test_media_show_accepts_a_delegated_user_token(): void
    {
        $client = new RecordingApolloHttpClient;
        $resource = new MediaResource($client);

        $resource->showWithUserToken('media-1', 'delegated-user-token', ['include' => 'formats']);

        self::assertSame('application', $client->lastRequest['auth']);
        self::assertSame('GET', $client->lastRequest['method']);
        self::assertSame('media/media-1', $client->lastRequest['endpoint']);
        self::assertSame(['include' => 'formats'], $client->lastRequest['query']);
        self::assertSame('delegated-user-token', $client->lastUserToken);
    }

    public function test_directory_application_grant_accepts_a_delegated_user_token(): void
    {
        $client = new RecordingApolloHttpClient;
        $resource = new DirectoriesResource($client);

        $resource->grantApplicationWithUserToken(
            'dir-1',
            'flare:playlist:1',
            DirectoryApplicationPermission::READ,
            'delegated-user-token',
        );

        self::assertSame('application', $client->lastRequest['auth']);
        self::assertSame('POST', $client->lastRequest['method']);
        self::assertSame('directories/dir-1/application-grants', $client->lastRequest['endpoint']);
        self::assertSame([
            'client_reference' => 'flare:playlist:1',
            'permission' => 'read',
        ], $client->lastRequest['payload']);
        self::assertSame('delegated-user-token', $client->lastUserToken);
    }

    /**
     * @param  class-string  $resourceClass
     * @param  array<int, mixed>  $arguments
     * @param  array<string, mixed>  $payload
     * @param  array<string, mixed>  $query
     */
    #[DataProvider('resourceRoutes')]
    public function test_proteus_resources_expose_contextual_actions_and_preserve_endpoint_behavior(
        string $resourceClass,
        string $action,
        array $arguments,
        string $auth,
        string $method,
        string $endpoint,
        array $payload = [],
        array $query = [],
        bool $raw = false,
    ): void {
        $client = new RecordingApolloHttpClient;
        $resource = new $resourceClass($client);

        $resource->{$action}(...$arguments);

        self::assertSame([
            'auth' => $auth,
            'method' => $method,
            'endpoint' => $endpoint,
            'payload' => $payload,
            'query' => $query,
            'raw' => $raw,
        ], $client->lastRequest);
    }

    /**
     * @return array<string, array{0: class-string, 1: string, 2: array<int, mixed>, 3: string, 4: string, 5: string, 6?: array<string, mixed>, 7?: array<string, mixed>, 8?: bool}>
     */
    public static function resourceRoutes(): array
    {
        return [
            'categories index' => [CategoriesResource::class, 'index', [['filter' => 'img']], 'application', 'GET', 'categories', [], ['filter' => 'img']],
            'categories store' => [CategoriesResource::class, 'store', [['name' => 'Images']], 'application', 'POST', 'configuration/categories', ['name' => 'Images']],
            'categories show' => [CategoriesResource::class, 'show', ['cat-1'], 'application', 'GET', 'configuration/categories/cat-1'],
            'categories update' => [CategoriesResource::class, 'update', ['cat-1', ['name' => 'Media']], 'application', 'PUT', 'configuration/categories/cat-1', ['name' => 'Media']],
            'categories delete' => [CategoriesResource::class, 'delete', ['cat-1'], 'application', 'DELETE', 'configuration/categories/cat-1'],
            'categories set default' => [CategoriesResource::class, 'setDefault', ['cat-1'], 'application', 'PATCH', 'configuration/categories/cat-1/default'],

            'directories index' => [DirectoriesResource::class, 'index', [['page' => 2]], 'user', 'GET', 'directories', [], ['page' => 2]],
            'directories create root' => [DirectoriesResource::class, 'create', [], 'user', 'GET', 'directories/create'],
            'directories create child' => [DirectoriesResource::class, 'create', ['dir-1'], 'user', 'GET', 'directories/create/dir-1'],
            'directories store' => [DirectoriesResource::class, 'store', [['name' => 'Assets']], 'user', 'POST', 'directories', ['name' => 'Assets']],
            'directories show' => [DirectoriesResource::class, 'show', ['dir-1'], 'user', 'GET', 'directories/dir-1'],
            'directories update' => [DirectoriesResource::class, 'update', ['dir-1', ['name' => 'Updated']], 'user', 'PUT', 'directories/dir-1', ['name' => 'Updated']],
            'directories delete' => [DirectoriesResource::class, 'delete', ['dir-1'], 'user', 'DELETE', 'directories/dir-1'],
            'directories grant application' => [DirectoriesResource::class, 'grantApplication', ['dir-1', 'flare:playlist:1', DirectoryApplicationPermission::READ], 'user', 'POST', 'directories/dir-1/application-grants', ['client_reference' => 'flare:playlist:1', 'permission' => 'read']],
            'directories update application grant' => [DirectoriesResource::class, 'updateApplicationGrant', ['grant-1', DirectoryApplicationPermission::WRITE], 'user', 'PATCH', 'directories/application-grants/grant-1', ['permission' => 'write']],
            'directories revoke application grant' => [DirectoriesResource::class, 'revokeApplicationGrant', ['grant-1'], 'user', 'DELETE', 'directories/application-grants/grant-1'],

            'presets index' => [PresetsResource::class, 'index', ['dir-1'], 'user', 'GET', 'directories/dir-1/presets'],
            'presets store' => [PresetsResource::class, 'store', ['dir-1', ['name' => 'Default']], 'user', 'POST', 'directories/dir-1/presets', ['name' => 'Default']],
            'presets show' => [PresetsResource::class, 'show', ['dir-1', '7'], 'user', 'GET', 'directories/dir-1/presets/7'],
            'presets update' => [PresetsResource::class, 'update', ['dir-1', '7', ['name' => 'New']], 'user', 'PUT', 'directories/dir-1/presets/7', ['name' => 'New']],
            'presets delete' => [PresetsResource::class, 'delete', ['dir-1', '7'], 'user', 'DELETE', 'directories/dir-1/presets/7'],

            'media index' => [MediaResource::class, 'index', [['type' => 'image']], 'user', 'GET', 'media', [], ['type' => 'image']],
            'media upload' => [MediaResource::class, 'upload', [['type' => 'image']], 'user', 'POST', 'media', ['type' => 'image']],
            'media create' => [MediaResource::class, 'create', [], 'user', 'GET', 'media/create'],
            'media tags' => [MediaResource::class, 'tags', [], 'user', 'GET', 'media/tags'],
            'media show' => [MediaResource::class, 'show', ['media-1'], 'user', 'GET', 'media/media-1'],
            'media show with directory grant' => [MediaResource::class, 'show', ['media-1', ['id_directory_application_grant' => 'grant-1']], 'user', 'GET', 'media/media-1', [], ['id_directory_application_grant' => 'grant-1']],
            'media delete' => [MediaResource::class, 'delete', ['media-1'], 'user', 'DELETE', 'media/media-1'],
            'media available formats' => [MediaResource::class, 'availableFormats', ['media-1'], 'user', 'GET', 'media/media-1/available-formats'],
            'media set default format' => [MediaResource::class, 'setDefaultFormat', ['media-1', ['format' => 'jpg']], 'user', 'POST', 'media/media-1/available-formats', ['format' => 'jpg']],
            'media transformation options' => [MediaResource::class, 'transformationOptions', ['media-1'], 'user', 'GET', 'media/media-1/request-transformations'],
            'media request transformations' => [MediaResource::class, 'requestTransformations', ['media-1', ['transformations' => ['thumb']]], 'user', 'POST', 'media/media-1/request-transformations', ['transformations' => ['thumb']]],
            'media set metadata' => [MediaResource::class, 'setMetadata', ['media-1', ['metadata' => ['k' => 'v']]], 'user', 'POST', 'media/media-1/set-metadata', ['metadata' => ['k' => 'v']]],
            'media store tags' => [MediaResource::class, 'storeTags', ['media-1', ['tags' => ['a']]], 'user', 'POST', 'media/media-1/tags/store', ['tags' => ['a']]],
            'media lightpath url' => [MediaResource::class, 'lightPathUrl', ['media-1', ['ext' => 'mp4', 'url_ttl_seconds' => 3600]], 'user', 'POST', 'media/media-1/lightpath-url', ['ext' => 'mp4', 'url_ttl_seconds' => 3600]],
            'media download' => [MediaResource::class, 'download', ['media-1', 'jpg'], 'user', 'GET', 'media/media-1/download', [], ['ext' => 'jpg'], true],
            'media thumbnail' => [MediaResource::class, 'thumbnail', ['media-1'], 'user', 'GET', 'media/media-1/download', [], ['ext' => 'thumb'], true],
            'media save local' => [MediaResource::class, 'saveLocal', ['media-1', 'webp'], 'user', 'GET', 'media/media-1/download', [], ['ext' => 'webp'], true],

            'lightpath extend grant' => [LightPathResource::class, 'extendGrant', ['grant-uuid', 3600], 'user', 'PATCH', 'lightpath/grants/grant-uuid/extend', ['url_ttl_seconds' => 3600]],
            'lightpath delete grant' => [LightPathResource::class, 'deleteGrant', ['grant-uuid'], 'user', 'DELETE', 'lightpath/grants/grant-uuid'],

            'metadata keys' => [MetadataResource::class, 'keys', ['author'], 'application', 'GET', 'media/metadata/author'],
            'metadata values' => [MetadataResource::class, 'values', ['author'], 'application', 'GET', 'media/metadata/author/values'],
            'metadata index' => [MetadataResource::class, 'index', ['media-1', ['page' => 1]], 'user', 'GET', 'media/media-1/metadata', [], ['page' => 1]],
            'metadata store' => [MetadataResource::class, 'store', ['media-1', ['key' => 'author']], 'user', 'POST', 'media/media-1/metadata', ['key' => 'author']],
            'metadata update' => [MetadataResource::class, 'update', ['media-1', ['key' => 'author']], 'user', 'PUT', 'media/media-1/metadata', ['key' => 'author']],
            'metadata show' => [MetadataResource::class, 'show', ['media-1', 'author'], 'user', 'GET', 'media/media-1/metadata/author'],
            'metadata delete' => [MetadataResource::class, 'delete', ['media-1', 'author'], 'user', 'DELETE', 'media/media-1/metadata/author'],
        ];
    }
}
