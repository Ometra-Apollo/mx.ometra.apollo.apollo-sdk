<?php

namespace Ometra\Apollo\Sdk\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Ometra\Apollo\Sdk\Facades\Apollo;
use Ometra\Caronte\Facades\Caronte;

final class SuiteApplicationController
{
    public function userApplications(Request $request): JsonResponse
    {
        return response()->json(Apollo::suite()->applications()->user());
    }
}
