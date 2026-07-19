<?php

declare(strict_types=1);

namespace Ometra\Apollo\Sdk\Facades;

use Illuminate\Support\Facades\Facade;
use Ometra\Apollo\Sdk\Apollo as ApolloEntrypoint;
use Ometra\Apollo\Sdk\Modules\Flare\FlareModule;
use Ometra\Apollo\Sdk\Modules\Ignis\IgnisModule;
use Ometra\Apollo\Sdk\Modules\Proteus\ProteusModule;
use Ometra\Apollo\Sdk\Modules\Pulse\PulseModule;

/**
 * @method static ProteusModule proteus()
 * @method static PulseModule pulse()
 * @method static FlareModule flare()
 * @method static IgnisModule ignis()
 *
 * @mixin ApolloEntrypoint
 *
 * @see ApolloEntrypoint
 */
final class Apollo extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return ApolloEntrypoint::class;
    }
}
