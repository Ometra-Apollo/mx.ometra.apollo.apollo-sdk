<?php

declare(strict_types=1);

namespace Ometra\Apollo\Sdk\Test;

use Ometra\Apollo\Sdk\Contracts\IgnisGroupContract;
use Ometra\Apollo\Sdk\DTO\ExternalGroupDTO;

/**
 * Default runnable implementation of {@see IgnisGroupContract}.
 *
 * Ships one hardcoded test group so the opt-in groups route works out of the
 * box. Host applications override `apollo.ignis_groups.implementation` with
 * their own contract implementation to expose real groups.
 */
final class DummyGroup implements IgnisGroupContract
{
    public function getGroups(): array
    {
        $group = new ExternalGroupDTO(
            name: 'Test Group',
            external_id: 'test_external_id',
            media_type: ['video'],
        );

        return [$group];
    }
}
