<?php

declare(strict_types=1);

namespace Ometra\Apollo\Sdk\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Ometra\Apollo\Sdk\Facades\Apollo;
use Ometra\Caronte\Facades\Caronte;

final class ProteusDirectoryController
{
    public function index(Request $request): JsonResponse
    {
        $query = $request->query();

        $query += [
            'uri_user' => Caronte::getUser()->uri_user ?? '',
            'recursive' => true,
            'format_search' => true,
            'only_tree' => $request->query('only_tree', false),
        ];

        return response()->json(Apollo::proteus()->directories()->index($query));
    }

    public function store(Request $request): JsonResponse
    {
        $payload = $request->validate([
            'name' => ['required', 'string'],
            'parent_id' => ['nullable', 'string'],
        ]);

        return response()->json(
            Apollo::proteus()->directories()->store($payload)
        );
    }
}
