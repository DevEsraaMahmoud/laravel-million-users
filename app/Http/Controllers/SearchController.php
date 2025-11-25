<?php

namespace App\Http\Controllers;

use App\Services\UserSearchService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function __construct(
        protected UserSearchService $userSearchService
    ) {
    }

    /**
     * Handle AJAX search requests for users.
     */
    public function users(Request $request): JsonResponse
    {
        $query = $request->get('query', '');
        $limit = max(1, min((int) $request->get('limit', 20), 100));

        $results = $this->userSearchService->searchCollection($query, $limit);

        return response()->json([
            'data' => $results,
        ]);
    }
}
