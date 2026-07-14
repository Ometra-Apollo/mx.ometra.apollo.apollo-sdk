<?php

declare(strict_types=1);

namespace Ometra\Apollo\Sdk\DTO;

final class IgnisCampaignContentScheduleDTO
{
    /**
     * @param array<int,string> $dow
     */
    public function __construct(
        public readonly string $dt_start,
        public readonly string $dt_end,
        public readonly array $dow,
    ) {}

    /**
     * @param array<string,mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            dt_start: (string) $data['dt_start'],
            dt_end: (string) $data['dt_end'],
            dow: array_values(array_map('strval', is_array($data['dow'] ?? null) ? $data['dow'] : [])),
        );
    }

    /**
     * @return array{dt_start:string,dt_end:string,dow:array<int,string>}
     */
    public function toArray(): array
    {
        return [
            'dt_start' => $this->dt_start,
            'dt_end' => $this->dt_end,
            'dow' => $this->dow,
        ];
    }
}
