<?php

declare(strict_types=1);

namespace Ometra\Apollo\Sdk\Modules\Proteus\Values;

final readonly class LightPathRecoveryResult
{
    /** @param array<string, mixed> $payload */
    public static function fromResponse(array $payload): self
    {
        $data = is_array($payload['data'] ?? null) ? $payload['data'] : $payload;

        return new self(
            deliveryId: (string) ($data['delivery_id'] ?? ''),
            version: (int) ($data['version'] ?? 0),
            outcome: (string) ($data['outcome'] ?? 'retry_scheduled'),
            recoveryStatus: (string) ($data['recovery_status'] ?? 'retry_scheduled'),
            errorCode: isset($data['error_code']) ? (string) $data['error_code'] : null,
            retryable: (bool) ($data['retryable'] ?? false),
            nextAttemptAt: isset($data['next_attempt_at']) ? (string) $data['next_attempt_at'] : null,
            correlationId: (string) ($payload['correlation_id'] ?? $data['correlation_id'] ?? ''),
            snapshot: $data,
        );
    }

    /** @param array<string, mixed> $snapshot */
    public function __construct(
        public string $deliveryId,
        public int $version,
        public string $outcome,
        public string $recoveryStatus,
        public ?string $errorCode,
        public bool $retryable,
        public ?string $nextAttemptAt,
        public string $correlationId,
        public array $snapshot,
    ) {}
}
