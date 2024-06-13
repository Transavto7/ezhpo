<?php

namespace App\Http\Controllers\Elements;

use App\Http\Controllers\Controller;
use App\Services\ElementsSearch\ElementsSearchServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class SearchElementsController extends Controller
{
    public function __invoke(Request $request, ElementsSearchServiceInterface $searchService): JsonResponse
    {
        $data = $searchService->search($request->input('identifier'));

        return response()->json($data);
    }
}
