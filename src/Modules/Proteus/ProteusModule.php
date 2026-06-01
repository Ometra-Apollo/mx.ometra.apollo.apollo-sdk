<?php

declare(strict_types=1);

namespace Ometra\Apollo\Sdk\Modules\Proteus;

use Ometra\Apollo\Sdk\Core\Config\ModuleConfigResolver;
use Ometra\Apollo\Sdk\Core\Http\ApolloHttpClient;
use Ometra\Apollo\Sdk\Modules\Proteus\Resources\CategoriesResource;
use Ometra\Apollo\Sdk\Modules\Proteus\Resources\DirectoriesResource;
use Ometra\Apollo\Sdk\Modules\Proteus\Resources\MediaResource;
use Ometra\Apollo\Sdk\Modules\Proteus\Resources\MetadataResource;
use Ometra\Apollo\Sdk\Modules\Proteus\Resources\PresetsResource;

final class ProteusModule
{
    private ?ApolloHttpClient $client = null;

    private bool $asApplication = false;

    public function __construct(private readonly ModuleConfigResolver $configResolver)
    {
    }

    public function asApplication(): static
    {
        $clone = clone $this;
        $clone->asApplication = true;
        $clone->client = null;

        return $clone;
    }

    /**
     * @return array{base_url_env: string, base_url: string}
     */
    public function config(): array
    {
        return $this->configResolver->resolve('proteus');
    }

    public function media(): MediaResource
    {
        return new MediaResource($this->client());
    }

    public function metadata(): MetadataResource
    {
        return new MetadataResource($this->client());
    }

    public function categories(): CategoriesResource
    {
        return new CategoriesResource($this->client());
    }

    public function directories(): DirectoriesResource
    {
        return new DirectoriesResource($this->client());
    }

    public function presets(): PresetsResource
    {
        return new PresetsResource($this->client());
    }

    private function client(): ApolloHttpClient
    {
        if ($this->client === null) {
            $this->client = new ApolloHttpClient(
                $this->config()['base_url'],
                $this->asApplication,
            );
        }

        return $this->client;
    }
}
