<?php

declare(strict_types=1);

namespace Ometra\Apollo\Sdk\Facades;

use Illuminate\Support\Facades\Facade;
use Ometra\Apollo\Sdk\Apollo as ApolloEntrypoint;

final class Apollo extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return ApolloEntrypoint::class;
    }
}
