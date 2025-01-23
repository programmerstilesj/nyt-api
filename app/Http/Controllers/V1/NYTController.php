<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\NYTBestSellersRequest;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * @OA\Info(
 *     title="NYT Best Sellers API",
 *     version="1.0.0",
 *     description="API for fetching NYT Best Sellers data"
 * )
 */
class NYTController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/v1/nyt/best-sellers",
     *     summary="Get NYT Best Sellers",
     *     tags={"NYT"},
     *     @OA\Parameter(
     *         name="author",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string"),
     *         description="Author name"
     *     ),
     *     @OA\Parameter(
     *         name="isbn",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string"),
     *         description="ISBN number - must be 10 or 13 digits each, separated by semi-colon"
     *     ),
     *     @OA\Parameter(
     *         name="title",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string"),
     *         description="Book title"
     *     ),
     *     @OA\Parameter(
     *         name="offset",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="integer"),
     *         description="Offset for pagination - must be multiple of 20"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="results", type="array", @OA\Items(type="object"))
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="error", type="string")
     *         )
     *     )
     * )
     */
    public function getBestSellers(NYTBestSellersRequest $request)
    {
        // Extract the query parameters from the request, filtering out the empty ones
        $query = array_filter($request->only(['author', 'isbn', 'title', 'offset']));
        // Add the API key to the query because NYT needs to know we're legit
        $query['api-key'] = config('services.nyt.api_key');

        // If we're in a local environment, let's not worry about SSL verification
        $options = [];
        if (config('app.env') === 'local') {
            $options['verify'] = false;
        }

        // Create a unique cache key based on the query parameters
        $cacheKey = 'nyt_best_sellers_'.md5(serialize($query));
        // Check if we already have a cached response, if not, fetch from NYT API and cache it
        $cachedResponse = Cache::remember($cacheKey, 3600, function () use ($options, $query) {
            // Make the HTTP request to the NYT API
            $response = Http::withOptions($options)
                ->get('https://api.nytimes.com/svc/books/v3/lists/best-sellers/history.json', $query);
            // Return the response status and body
            return [
                'status' => $response->status(),
                'body' => $response->json()
            ];
        });

        // If we got a valid response, return it as JSON
        if (is_array($cachedResponse) && isset($cachedResponse['status']) && isset($cachedResponse['body'])) {
            return response()->json($cachedResponse['body'], $cachedResponse['status']);
        }

        // Log the error for debugging purposes
        Log::error('Unable to fetch data from NYT API', ['query' => $query, 'response' => $cachedResponse]);

        // If something went wrong, return an error message
        return response()->json(['error' => 'Unable to fetch data from NYT API'], $cachedResponse['status'] ?: 500);
    }
}
