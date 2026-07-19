<?php

declare(strict_types=1);

namespace Ometra\Apollo\Sdk\Modules\Ignis;

use Ometra\Apollo\Sdk\Core\Config\ModuleConfigResolver;
use Ometra\Apollo\Sdk\Core\Http\ApolloHttpClient;
use Ometra\Apollo\Sdk\Modules\Ignis\Resources\CampaignsResource;
use Ometra\Apollo\Sdk\Modules\Ignis\Resources\ContentHitsResource;

/**
 * Ignis module entrypoint.
 *
 * Exposed resources:
 * - campaigns()
 * - contentHits()
 */
final class IgnisModule
{
    public function __construct(private readonly ModuleConfigResolver $configResolver) {}

    /**
     * @return array{base_url: string}
     */
    public function config(): array
    {
        return $this->configResolver->resolve('ignis');
    }

    public function campaigns(): CampaignsResource
    {
        return new CampaignsResource($this->client());
    }

    public function contentHits(): ContentHitsResource
    {
        return new ContentHitsResource($this->client());
    }

    private function client(): ApolloHttpClient
    {
        return new ApolloHttpClient($this->config()['base_url']);
    }
}
