<?php

declare(strict_types=1);

namespace Ometra\Apollo\Sdk\Modules\Proteus;

use Ometra\Apollo\Sdk\Core\Config\ModuleConfigResolver;
use Ometra\Apollo\Sdk\Core\Http\ApolloHttpClient;
use Ometra\Apollo\Sdk\Modules\Proteus\Resources\CategoriesResource;
use Ometra\Apollo\Sdk\Modules\Proteus\Resources\DirectoriesCollectionResource;
use Ometra\Apollo\Sdk\Modules\Proteus\Resources\DirectoryResource;
use Ometra\Apollo\Sdk\Modules\Proteus\Resources\LightPathResource;
use Ometra\Apollo\Sdk\Modules\Proteus\Resources\MediaCollectionResource;
use Ometra\Apollo\Sdk\Modules\Proteus\Resources\MediaResource;

final class ProteusModule
{
    private ?ApolloHttpClient $client = null;

    private bool $asApplication = false;

    public function __construct(private readonly ModuleConfigResolver $configResolver)
    {
        //
    }

    public function asApplication(): static
    {
        $clone = clone $this;
        $clone->asApplication = true;
        $clone->client = null;

        return $clone;
    }

    /**
     * @return ($mediaId is null ? MediaCollectionResource : MediaResource)
     */
    public function media(?string $mediaId = null): MediaCollectionResource|MediaResource
    {
        return $mediaId === null
            ? new MediaCollectionResource($this->client())
            : new MediaResource($this->client(), $mediaId);
    }

    public function lightPath(string $lightPathGrantId): LightPathResource
    {
        return new LightPathResource($this->client(), $lightPathGrantId);
    }

    public function categories(): CategoriesResource
    {
        return new CategoriesResource($this->client());
    }

    /**
     * @return ($directoryId is null ? DirectoriesCollectionResource : DirectoryResource)
     */
    public function directories(?string $directoryId = null): DirectoriesCollectionResource|DirectoryResource
    {
        return $directoryId === null
            ? new DirectoriesCollectionResource($this->client())
            : new DirectoryResource($this->client(), $directoryId);
    }

    private function client(): ApolloHttpClient
    {
        if ($this->client === null) {
            $this->client = new ApolloHttpClient(
                $this->configResolver->resolve('proteus')['base_url'],
                $this->asApplication,
            );
        }

        return $this->client;
    }
}
