<?php

declare(strict_types=1);

namespace Ometra\Apollo\Sdk\DTO;

final class IgnisCampaignContentDTO
{
    /**
     * @param array<int,IgnisCampaignContentScheduleDTO> $content_schedules
     */
    public function __construct(
        public readonly string $id_media,
        public readonly string $type,
        public readonly string $media_type,
        public readonly string $ext,
        public readonly string $name,
        public readonly array $content_schedules,
    ) {}

    /**
     * @param array<string,mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            id_media: (string) $data['id_media'],
            type: (string) ($data['type'] ?? $data['media_type'] ?? ''),
            media_type: (string) ($data['media_type'] ?? $data['type'] ?? ''),
            ext: (string) ($data['ext'] ?? ''),
            name: (string) $data['name'],
            content_schedules: array_map(
                static fn (array $schedule): IgnisCampaignContentScheduleDTO => IgnisCampaignContentScheduleDTO::fromArray($schedule),
                array_values(array_filter(
                    is_array($data['content_schedules'] ?? null) ? $data['content_schedules'] : [],
                    'is_array'
                ))
            ),
        );
    }

    /**
     * @return array{id_media:string,type:string,media_type:string,ext:string,name:string,content_schedules:array<int,array{dt_start:string,dt_end:string,dow:array<int,string>}>}
     */
    public function toArray(): array
    {
        return [
            'id_media' => $this->id_media,
            'type' => $this->type,
            'media_type' => $this->media_type,
            'ext' => $this->ext,
            'name' => $this->name,
            'content_schedules' => array_map(
                static fn (IgnisCampaignContentScheduleDTO $schedule): array => $schedule->toArray(),
                $this->content_schedules
            ),
        ];
    }
}
