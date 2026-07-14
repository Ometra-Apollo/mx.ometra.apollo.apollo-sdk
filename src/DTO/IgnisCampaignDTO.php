<?php

declare(strict_types=1);

namespace Ometra\Apollo\Sdk\DTO;

final class IgnisCampaignDTO
{
    public function __construct(
        public readonly int $id_campaign,
        public readonly string $name,
        public readonly string $dt_start,
        public readonly string $dt_end,
        public readonly ?IgnisCampaignPlayModifiersDTO $play_modifiers = null,
    ) {}

    /**
     * @param array<string,mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            id_campaign: (int) $data['id_campaign'],
            name: (string) $data['name'],
            dt_start: (string) $data['dt_start'],
            dt_end: (string) $data['dt_end'],
            play_modifiers: isset($data['play_modifiers']) && is_array($data['play_modifiers'])
                ? IgnisCampaignPlayModifiersDTO::fromArray($data['play_modifiers'])
                : null,
        );
    }

    /**
     * @return array{id_campaign:int,name:string,dt_start:string,dt_end:string,play_modifiers?:array{frequency?:string}}
     */
    public function toArray(): array
    {
        return array_filter([
            'id_campaign' => $this->id_campaign,
            'name' => $this->name,
            'dt_start' => $this->dt_start,
            'dt_end' => $this->dt_end,
            'play_modifiers' => $this->play_modifiers?->toArray(),
        ], static fn (mixed $value): bool => $value !== null);
    }
}
