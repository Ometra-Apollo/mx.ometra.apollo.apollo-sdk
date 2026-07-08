<?php

declare(strict_types=1);

namespace Ometra\Apollo\Sdk\DTO;

final class IgnisCampaignPlayModifiersDTO
{
    public function __construct(
        public readonly ?string $frequency = null,
    ) {}

    /**
     * @param array<string,mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            frequency: isset($data['frequency']) ? (string) $data['frequency'] : null,
        );
    }

    /**
     * @return array{frequency?:string}
     */
    public function toArray(): array
    {
        return array_filter([
            'frequency' => $this->frequency,
        ], static fn (mixed $value): bool => $value !== null);
    }
}
