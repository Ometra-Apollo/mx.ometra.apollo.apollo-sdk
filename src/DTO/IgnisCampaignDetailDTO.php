<?php

declare(strict_types=1);

namespace Ometra\Apollo\Sdk\DTO;

final class IgnisCampaignDetailDTO
{
    /**
     * @param  array<int,IgnisCampaignContentDTO>  $contents
     */
    public function __construct(
        public readonly int $id_campaign,
        public readonly string $name,
        public readonly string $dt_start,
        public readonly string $dt_end,
        public readonly array $contents,
    ) {}

    /**
     * @param  array<string,mixed>  $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            id_campaign: (int) $data['id_campaign'],
            name: (string) $data['name'],
            dt_start: (string) $data['dt_start'],
            dt_end: (string) $data['dt_end'],
            contents: array_map(
                static fn (array $content): IgnisCampaignContentDTO => IgnisCampaignContentDTO::fromArray($content),
                array_values(array_filter(is_array($data['contents'] ?? null) ? $data['contents'] : [], 'is_array'))
            ),
        );
    }

    /**
     * @return array{id_campaign:int,name:string,dt_start:string,dt_end:string,contents:array<int,array{id_media:string,type:string,name:string,content_schedules:array<int,array{dt_start:string,dt_end:string,dow:array<int,string>}>}>}
     */
    public function toArray(): array
    {
        return [
            'id_campaign' => $this->id_campaign,
            'name' => $this->name,
            'dt_start' => $this->dt_start,
            'dt_end' => $this->dt_end,
            'contents' => array_map(
                static fn (IgnisCampaignContentDTO $content): array => $content->toArray(),
                $this->contents
            ),
        ];
    }
}
