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
use Ometra\Caronte\Caronte;
use Ometra\Caronte\Support\CaronteHttpClient;
use PHPUnit\Framework\TestCase;

final class ApolloHttpClientTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $app = new Container();
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

        $app->instance(Caronte::class, new class {
            public function getToken(): object
            {
                return new class {
                    public function toString(): string
                    {
                        return 'user-token';
                    }
                };
            }
        });

        $tenantContext = new TenantContext();
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

    public function testApolloHttpClientPreservesCaronteHttpClientContract(): void
    {
        $client = new ApolloHttpClient('https://proteus.test/api');

        self::assertInstanceOf(CaronteHttpClient::class, $client);
    }

    public function testUserRequestSendsGroupUserAndTenantHeadersFromCaronte(): void
    {
        $client = new ApolloHttpClient('https://proteus.test/api');

        $response = $client->userRequest('GET', 'media', query: ['type' => 'image']);

        self::assertSame(['accepted' => true], $response['data']);
        Http::assertSent(fn(Request $request): bool => $request->url() === 'https://proteus.test/api/media?type=image'
            && ! $request->hasHeader('X-Application-Token')
            && $request->hasHeader('X-Group-Token')
            && $request->hasHeader('X-User-Token', 'user-token')
            && $request->hasHeader('X-Tenant-Id', 'tenant-42'));
    }

    public function testRequestWithoutCaronteConfigThrowsBeforeTransport(): void
    {
        $app = new class extends Container {
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

    public function testApplicationRequestOmitsUserTokenAndUsesGroupAuthentication(): void
    {
        $client = new ApolloHttpClient('https://proteus.test/api');

        $response = $client->applicationRequest('POST', 'categories', ['name' => 'Images']);

        self::assertSame(['accepted' => true], $response['data']);
        Http::assertSent(fn(Request $request): bool => $request->url() === 'https://proteus.test/api/categories'
            && ! $request->hasHeader('X-Application-Token')
            && $request->hasHeader('X-Group-Token')
            && ! $request->hasHeader('X-User-Token')
            && $request->hasHeader('X-Tenant-Id', 'tenant-42'));
    }

    public function testApplicationRequestCanIncludeExplicitUserTokenWhenProvided(): void
    {
        $client = new ApolloHttpClient('https://proteus.test/api');

        $response = $client->applicationRequest(
            'POST',
            'categories',
            ['name' => 'Images'],
            userToken: 'delegated-user-token'
        );

        self::assertSame(['accepted' => true], $response['data']);
        Http::assertSent(fn(Request $request): bool => $request->url() === 'https://proteus.test/api/categories'
            && ! $request->hasHeader('X-Application-Token')
            && $request->hasHeader('X-Group-Token')
            && $request->hasHeader('X-User-Token', 'delegated-user-token')
            && $request->hasHeader('X-Tenant-Id', 'tenant-42'));
    }

    public function testUserRequestWithAsApplicationFlagOmitsUserTokenAndUsesGroupAuthentication(): void
    {
        $client = new ApolloHttpClient('https://proteus.test/api', asApplication: true);

        $response = $client->userRequest('GET', 'media', query: ['type' => 'image']);

        self::assertSame(['accepted' => true], $response['data']);
        Http::assertSent(fn(Request $request): bool => $request->url() === 'https://proteus.test/api/media?type=image'
            && ! $request->hasHeader('X-Application-Token')
            && $request->hasHeader('X-Group-Token')
            && ! $request->hasHeader('X-User-Token')
            && $request->hasHeader('X-Tenant-Id', 'tenant-42'));
    }

    public function testApplicationTokenIsUsedWhenGroupIsNotConfigured(): void
    {
        config()->set('caronte.application_group_id', '');
        config()->set('caronte.application_group_secret', '');

        $client = new ApolloHttpClient('https://proteus.test/api');
        $client->applicationRequest('GET', 'media');

        Http::assertSent(fn(Request $request): bool => $request->hasHeader('X-Application-Token')
            && ! $request->hasHeader('X-Group-Token'));
    }

    public function testUserRawRequestReturnsTheInheritedUnparsedResponse(): void
    {
        Http::fake([
            'https://binary.test/api/media/asset/download' => Http::response('binary-data', 200),
        ]);

        $client = new ApolloHttpClient('https://binary.test/api');
        $response = $client->userRawRequest('GET', 'media/asset/download');

        self::assertInstanceOf(Response::class, $response);
        self::assertSame('binary-data', $response->body());
        Http::assertSent(fn(Request $request): bool => $request->hasHeader('Accept', '*/*')
            && $request->hasHeader('X-Group-Token')
            && ! $request->hasHeader('X-Application-Token')
            && $request->hasHeader('X-User-Token', 'user-token'));
    }

    public function testUploadUsesInheritedMultipartTransport(): void
    {
        $client = new ApolloHttpClient('https://proteus.test/api', asApplication: true);
        $client->userRequest('POST', 'media', [
            'file' => UploadedFile::fake()->createWithContent('image.txt', 'image-data'),
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
