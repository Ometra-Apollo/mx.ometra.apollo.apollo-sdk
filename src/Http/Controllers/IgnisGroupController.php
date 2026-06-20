<?php

declare(strict_types=1);

namespace Ometra\Apollo\Sdk\Http\Controllers;

use Ometra\Apollo\Sdk\Contracts\IgnisGroupContract;
use Ometra\Apollo\Sdk\DTO\ExternalGroupDTO;

final class IgnisGroupController
{
    /**
     * @return \Illuminate\Http\JsonResponse Raw JSON array of {@see ExternalGroupDTO::toArray()} shapes.
     */
    public function index(IgnisGroupContract $groups)
    {
        return response()->json(
            array_map(fn (ExternalGroupDTO $group): array => $group->toArray(), $groups->getGroups())
        );
    }
}
