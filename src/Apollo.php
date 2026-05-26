<?php

declare(strict_types=1);

namespace Ometra\Apollo\Sdk;

use Ometra\Apollo\Sdk\Modules\Flare\FlareModule;
use Ometra\Apollo\Sdk\Modules\Ignis\IgnisModule;
use Ometra\Apollo\Sdk\Modules\Proteus\ProteusModule;
use Ometra\Apollo\Sdk\Modules\Pulse\PulseModule;

final class Apollo
{
    public function __construct(
        private readonly ProteusModule $proteus,
        private readonly PulseModule $pulse,
        private readonly FlareModule $flare,
        private readonly IgnisModule $ignis,
    ) {
    }

    public function proteus(): ProteusModule
    {
        return $this->proteus;
    }

    public function pulse(): PulseModule
    {
        return $this->pulse;
    }

    public function flare(): FlareModule
    {
        return $this->flare;
    }

    public function ignis(): IgnisModule
    {
        return $this->ignis;
    }
}
