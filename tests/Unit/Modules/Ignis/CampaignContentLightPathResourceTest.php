<?php

declare(strict_types=1);

use Equidna\BeeHive\Tenancy\TenantContext;
use Illuminate\Config\Repository;
use Illuminate\Container\Container;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Facade;
use Illuminate\Support\Facades\Http;
use Ometra\Apollo\Sdk\Core\Http\ApolloHttpClient;
use Ometra\Apollo\Sdk\Modules\Ignis\Resources\CampaignContentLightPathResource;
use PHPUnit\Framework\TestCase;

final class CampaignContentLightPathResourceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $app = new Container;
        Container::setInstance($app);
        Facade::clearResolvedInstances();
        Facade::setFacadeApplication($app);
        $app->instance('config', new Repository(['caronte' => [
            'app_cn' => 'sdk-test',
            'app_secret' => 'application-secret-32-chars-minimum-ok',
            'application_group_id' => 'apollo-suite',
            'application_group_secret' => 'group-secret-32-chars-minimum-ok',
            'tls_verify' => false,
        ]]));
        $tenant = new TenantContext;
        $tenant->set('tenant-42');
        $app->instance(TenantContext::class, $tenant);
        Http::preventStrayRequests();
        Http::fake(['https://ignis.test/api/*' => Http::response([
            'status' => 200,
            'message' => 'ok',
            'data' => ['id_media' => 'media/one', 'status' => 'refreshed'],
            'errors' => [],
        ])]);
    }

    protected function tearDown(): void
    {
        Facade::clearResolvedInstances();
        Facade::setFacadeApplication(null);
        Container::setInstance(null);
        parent::tearDown();
    }

    public function test_refresh_uses_campaign_endpoint_and_application_authentication(): void
    {
        $resource = new CampaignContentLightPathResource(
            new ApolloHttpClient('https://ignis.test/api'),
            'group-one',
            17,
        );

        $response = $resource->refresh('media-one');

        self::assertSame('refreshed', $response['data']['status']);
        Http::assertSent(fn (Request $request): bool => $request->method() === 'POST'
            && $request->url() === 'https://ignis.test/api/groups/group-one/campaigns/17/contents/media-one/refresh-lightpath'
            && $request->hasHeader('X-Group-Token')
            && ! $request->hasHeader('X-User-Token')
        );
    }
}
