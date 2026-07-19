<?php

declare(strict_types=1);

namespace Ometra\Apollo\Sdk\DTO;

use Illuminate\Support\Str;
use Ometra\Apollo\Sdk\Enums\MediaTypeEnum;

final class ExternalGroupDTO
{
    public string $provider_id;

    /**
     * @param  string  $name  Group display name.
     * @param  string  $external_id  Host-external group identifier.
     * @param  array<string>  $media_type  Raw media type values; normalized to {@see MediaTypeEnum} values in-place.
     * @param  array<string,mixed>|null  $play_modifiers  Optional playback modifiers; dropped from {@see toArray()} when null.
     */
    public function __construct(
        public readonly string $name,
        public readonly string $external_id,
        public array $media_type,
        public readonly ?array $play_modifiers = null,
    ) {
        $this->provider_id = Str::slug(config('app.name'));
        $this->media_type = collect($media_type)->mapInto(MediaTypeEnum::class)->pluck('value')->toArray();
    }

    public static function fromArray(array $data): self
    {
        return new self(
            name: $data['name'],
            external_id: $data['external_id'],
            media_type: $data['media_type'],
            play_modifiers: $data['play_modifiers'] ?? null,
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'name' => $this->name,
            'external_id' => $this->external_id,
            'media_type' => $this->media_type,
            'provider_id' => $this->provider_id,
            'play_modifiers' => $this->play_modifiers,
        ], static fn (mixed $value): bool => $value !== null);
    }
}
