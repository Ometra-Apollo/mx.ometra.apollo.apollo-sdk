<?php

declare(strict_types=1);

namespace Ometra\Apollo\Sdk\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Ometra\Apollo\Sdk\Facades\Apollo;

final class SuiteApplicationController
{
    public function userApplications(): JsonResponse
    {
        return response()->json(Apollo::suite()->applications()->user());
    }
}
