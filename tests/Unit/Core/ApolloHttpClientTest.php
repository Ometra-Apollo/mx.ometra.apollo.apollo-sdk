<?php

declare(strict_types=1);

use Equidna\BeeHive\Tenancy\TenantContext;
use Illuminate\Config\Repository;
use Illuminate\Container\Container;
use Illuminate\Http\Client\Request;
use Illuminate\Http\Client\Response;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Facade;
use Illuminate\Support\Facades\Http;
use Ometra\Apollo\Sdk\Core\Http\ApolloHttpClient;
use Ometra\Apollo\Sdk\Modules\Proteus\Resources\LightPathResource;
use Ometra\Caronte\Caronte;
use Ometra\Caronte\Support\CaronteHttpClient;
use PHPUnit\Framework\TestCase;

final class ApolloHttpClientTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $app = new Container;
        Container::setInstance($app);
        Facade::clearResolvedInstances();
        Facade::setFacadeApplication($app);

        $app->instance('config', new Repository([
            'caronte' => [
                'app_cn' => 'apollo-sdk-test',
                'app_secret' => 'application-secret-32-chars-minimum-ok',
                'application_group_id' => 'group-alpha',
                'application_group_secret' => 'group-secret-32-chars-minimum-ok',
                'tls_verify' => false,
                'http' => [
                    'timeout' => 7,
                    'retries' => 2,
                    'retry_sleep' => 25,
                ],
            ],
        ]));

        $app->instance(Caronte::class, new class
        {
            public function getToken(): object
            {
                return new class
                {
                    public function toString(): string
                    {
                        return 'user-token';
                    }
                };
            }
        });

        $tenantContext = new TenantContext;
        $tenantContext->set('tenant-42');
        $app->instance(TenantContext::class, $tenantContext);

        Http::preventStrayRequests();
        Http::fake([
            'https://proteus.test/api/*' => Http::response([
                'status' => 200,
                'message' => 'ok',
                'data' => ['accepted' => true],
                'errors' => [],
            ]),
        ]);
    }

    protected function tearDown(): void
    {
        Facade::clearResolvedInstances();
        Facade::setFacadeApplication(null);
        Container::setInstance(null);

        parent::tearDown();
    }

    public function test_apollo_http_client_preserves_caronte_http_client_contract(): void
    {
        $client = new ApolloHttpClient('https://proteus.test/api');

        self::assertInstanceOf(CaronteHttpClient::class, $client);
    }

    public function test_user_request_sends_group_user_and_tenant_headers_from_caronte(): void
    {
        $client = new ApolloHttpClient('https://proteus.test/api');

        $response = $client->userRequest('GET', 'media', query: ['type' => 'image']);

        self::assertSame(['accepted' => true], $response['data']);
        Http::assertSent(fn (Request $request): bool => $request->url() === 'https://proteus.test/api/media?type=image'
            && ! $request->hasHeader('X-Application-Token')
            && $request->hasHeader('X-Group-Token')
            && $request->hasHeader('X-User-Token', 'user-token')
            && $request->hasHeader('X-Tenant-Id', 'tenant-42'));
    }

    public function test_request_without_caronte_config_throws_before_transport(): void
    {
        $app = new class extends Container
        {
            public function runningInConsole(): bool
            {
                return true;
            }

            public function runningUnitTests(): bool
            {
                return true;
            }
        };

        Container::setInstance($app);
        Facade::clearResolvedInstances();
        Facade::setFacadeApplication($app);

        $app->instance('config', new Repository([]));

        $client = new ApolloHttpClient('https://proteus.test/api');

        $this->expectException(\Throwable::class);

        $client->userRequest('GET', 'media');
    }

    public function test_application_request_omits_user_token_and_uses_group_authentication(): void
    {
        $client = new ApolloHttpClient('https://proteus.test/api');

        $response = $client->applicationRequest('POST', 'categories', ['name' => 'Images']);

        self::assertSame(['accepted' => true], $response['data']);
        Http::assertSent(fn (Request $request): bool => $request->url() === 'https://proteus.test/api/categories'
            && ! $request->hasHeader('X-Application-Token')
            && $request->hasHeader('X-Group-Token')
            && ! $request->hasHeader('X-User-Token')
            && $request->hasHeader('X-Tenant-Id', 'tenant-42'));
    }

    public function test_user_request_with_as_application_flag_omits_user_token_and_uses_group_authentication(): void
    {
        $client = new ApolloHttpClient('https://proteus.test/api', asApplication: true);

        $response = $client->userRequest('GET', 'media', query: ['type' => 'image']);

        self::assertSame(['accepted' => true], $response['data']);
        Http::assertSent(fn (Request $request): bool => $request->url() === 'https://proteus.test/api/media?type=image'
            && ! $request->hasHeader('X-Application-Token')
            && $request->hasHeader('X-Group-Token')
            && ! $request->hasHeader('X-User-Token')
            && $request->hasHeader('X-Tenant-Id', 'tenant-42'));
    }

    public function test_light_path_management_uses_application_context_when_requested(): void
    {
        $resource = new LightPathResource(
            new ApolloHttpClient('https://proteus.test/api', asApplication: true),
            'grant-uuid',
        );

        $resource->extend(3600);

        Http::assertSent(fn (Request $request): bool => $request->url() === 'https://proteus.test/api/lightpath/grants/grant-uuid/extend'
            && $request->method() === 'PATCH'
            && $request['url_ttl_seconds'] === 3600
            && $request->hasHeader('X-Group-Token')
            && ! $request->hasHeader('X-User-Token'));
    }

    public function test_application_token_is_used_when_group_is_not_configured(): void
    {
        config()->set('caronte.application_group_id', '');
        config()->set('caronte.application_group_secret', '');

        $client = new ApolloHttpClient('https://proteus.test/api');
        $client->applicationRequest('GET', 'media');

        Http::assertSent(fn (Request $request): bool => $request->hasHeader('X-Application-Token')
            && ! $request->hasHeader('X-Group-Token'));
    }

    public function test_user_raw_request_returns_the_inherited_unparsed_response(): void
    {
        Http::fake([
            'https://binary.test/api/media/asset/download' => Http::response('binary-data', 200),
        ]);

        $client = new ApolloHttpClient('https://binary.test/api');
        $response = $client->userRawRequest('GET', 'media/asset/download');

        self::assertInstanceOf(Response::class, $response);
        self::assertSame('binary-data', $response->body());
        Http::assertSent(fn (Request $request): bool => $request->hasHeader('Accept', '*/*')
            && $request->hasHeader('X-Group-Token')
            && ! $request->hasHeader('X-Application-Token')
            && $request->hasHeader('X-User-Token', 'user-token'));
    }

    public function test_upload_uses_inherited_multipart_transport(): void
    {
        $path = dirname(__DIR__, 2).'/Fixtures/image.txt';

        $client = new ApolloHttpClient('https://proteus.test/api', asApplication: true);
        $client->userRequest('POST', 'media', [
            'file' => new UploadedFile($path, 'image.txt', 'text/plain', null, true),
            'published' => true,
        ]);

        Http::assertSent(function (Request $request): bool {
            $body = $request->body();

            return str_contains($request->header('Content-Type')[0] ?? '', 'multipart/form-data')
                && str_contains($body, 'filename="image.txt"')
                && str_contains($body, 'image-data')
                && ! $request->hasHeader('X-User-Token');
        });
    }
}
