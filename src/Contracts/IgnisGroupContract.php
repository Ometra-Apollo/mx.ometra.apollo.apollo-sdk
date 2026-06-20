<?php

declare(strict_types=1);

namespace Ometra\Apollo\Sdk\Contracts;

use Ometra\Apollo\Sdk\DTO\ExternalGroupDTO;

/**
 * Interface for retrieving groups exposed by the host application.
 *
 * Defines the methods and constants required to fetch groups that the host
 * advertises through the opt-in `GET /{prefix}/groups` route.
 */
interface IgnisGroupContract
{
    /**
     * Available modifiers for group playback.
     *
     * @var string[] List of allowed modifier keys (e.g., 'frequency', 'max_rep_count').
     */
    public const AVAILABLE_PLAY_MODIFIERS = ['frequency', 'max_rep_count'];

    /**
     * Retrieves all available groups exposed by the host.
     *
     * @return ExternalGroupDTO[] Array of external group DTO objects.
     */
    public function getGroups(): array;
}
